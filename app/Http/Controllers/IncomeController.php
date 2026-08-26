<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Student;
use App\Models\Tagihan;
use App\Jobs\SendWhatsappMessageJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $selectedClass = $request->input('kelas');

        $query = Student::query()->with(['tagihans' => function ($q) {
            $q->orderBy('urutan', 'asc');
        }])->where('status', 'aktif');
        $classes = Student::query()->select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');
        $selectedClass = $request->kelas ?? '';
        
        $activeYear = \App\Models\AcademicYear::getActive();
        if (!$activeYear) {
            return redirect()->route('academic-years.index')->with('error', 'Silakan setup Tahun Ajaran terlebih dahulu.');
        }

        // Ambil target kelas dari database untuk tahun aktif
        $targets = \App\Models\ClassTarget::where('academic_year_id', $activeYear->id)->get();
        $classTargets = [];
        foreach ($targets as $t) {
            $classTargets[$t->kelas][$t->urutan] = [
                'name' => $t->nama_tagihan,
                'nominal' => $t->nominal
            ];
        }

        // Ambil data siswa sesuai kelas, muat tagihan yang sesuai tahun aktif
        $studentsQuery = Student::query()->with(['tagihans' => function($q) use ($activeYear) {
            $q->where('academic_year_id', $activeYear->id);
        }])->where('status', 'aktif');

        if ($selectedClass) {
            $studentsQuery->where('kelas', $selectedClass);
        }
        $students = $studentsQuery->orderBy('nama', 'asc')->get();

        // Riwayat Transaksi Terbaru (hanya yang tahun aktif)
        $incomes = Income::with(['student', 'tagihan'])
            ->where('academic_year_id', $activeYear->id)
            ->latest('tanggal')
            ->take(10)
            ->get();

        // Ambil data kuitansi jika ada setelah transaksi berhasil dicatat
        $receipt = null;
        if (session('show_receipt_id')) {
            $receipt = Income::query()->with(['student', 'tagihan'])->find(session('show_receipt_id'));
        }

        return view('incomes.index', compact('students', 'classes', 'classTargets', 'incomes', 'selectedClass', 'receipt'));
    }

    public function updateTargetPembayaran(Request $request)
    {
        $validated = $request->validate([
            'kelas' => 'required|string',
            'nama' => 'required|array',
            'nama.*' => 'required|string',
            'nominal' => 'required|array',
            'nominal.*' => 'required|numeric|min:0',
        ]);

        $activeYear = \App\Models\AcademicYear::getActive();
        if (!$activeYear) {
            return redirect()->back()->with('error', 'Silakan setup Tahun Ajaran terlebih dahulu.');
        }

        $students = Student::query()->where('kelas', $request->kelas)->get();

        if ($students->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada siswa di kelas ' . $request->kelas);
        }

        // Ambil data nama dan nominal
        $namas = $request->input('nama');
        $nominals = $request->input('nominal');
        $count = count($namas);

        DB::transaction(function () use ($activeYear, $students, $namas, $nominals, $count, $request) {
            // Hapus target kelas yang urutannya melebihi jumlah target baru
            \App\Models\ClassTarget::where('academic_year_id', $activeYear->id)
                ->where('kelas', $request->kelas)
                ->where('urutan', '>', $count)
                ->delete();

            // Simpan konfigurasi kelas ke DB ClassTarget
            for ($i = 0; $i < $count; $i++) {
                $urutan = $i + 1;
                \App\Models\ClassTarget::updateOrCreate(
                    [
                        'academic_year_id' => $activeYear->id,
                        'kelas' => $request->kelas,
                        'urutan' => $urutan
                    ],
                    [
                        'nama_tagihan' => $namas[$i],
                        'nominal' => (float)$nominals[$i]
                    ]
                );
            }

            // Buat atau perbarui tagihan untuk semua siswa di kelas ini pada tahun ajaran aktif
            foreach ($students as $student) {
                // Hapus tagihan yang urutannya lebih dari jumlah target, hanya jika belum ada pembayaran
                Tagihan::where('academic_year_id', $activeYear->id)
                    ->where('student_id', $student->id)
                    ->where('urutan', '>', $count)
                    ->where('total_dibayar', 0)
                    ->delete();

                for ($i = 0; $i < $count; $i++) {
                    $urutan = $i + 1;
                    $nama_tagihan = $namas[$i];
                    $nominal = $nominals[$i];

                    $tagihan = Tagihan::query()->updateOrCreate(
                        [
                            'academic_year_id' => $activeYear->id,
                            'student_id' => $student->id,
                            'urutan' => $urutan,
                        ],
                        [
                            'nama_tagihan' => $nama_tagihan,
                            'total_tagihan' => $nominal,
                        ]
                    );

                    // Update status berdasarkan total_dibayar
                    if ($tagihan->total_dibayar >= $tagihan->total_tagihan) {
                        $tagihan->status = 'lunas';
                    } elseif ($tagihan->total_dibayar > 0) {
                        $tagihan->status = 'mencicil';
                    } else {
                        $tagihan->status = 'belum_bayar';
                    }
                    $tagihan->save();
                }
            }
        });

        return redirect()->route('incomes.index', ['kelas' => $request->kelas])->with('success', 'Pengaturan jenis pembayaran kelas ' . $request->kelas . ' berhasil diperbarui!');
    }

    public function create()
    {
        $students = Student::query()->where('status', 'aktif')->orderBy('nama', 'asc')->get();
        return view('incomes.create', compact('students'));
    }

    public function createOther()
    {
        return view('incomes.create_other');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'tagihan_id' => 'nullable|exists:tagihans,id',
            'tanggal' => 'required|date',
            'jenis_pembayaran' => 'required|string',
            'nominal' => 'required|numeric|min:0',
            'bukti' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'keterangan' => 'nullable|string',
        ]);

        if ($request->hasFile('bukti')) {
            $validated['bukti'] = $request->file('bukti')->store('bukti_pemasukan', 'public');
        }
        
        $activeYear = \App\Models\AcademicYear::getActive();
        if ($activeYear) {
            $validated['academic_year_id'] = $activeYear->id;
        }

        $income = null;
        DB::transaction(function () use ($validated, $request, &$income) {
            $income = Income::query()->create($validated);

            if ($request->filled('tagihan_id')) {
                $tagihan = Tagihan::query()->find($request->tagihan_id);
                
                if ($tagihan) {
                    $tagihan->total_dibayar += $request->nominal;

                    if ($tagihan->total_dibayar >= $tagihan->total_tagihan) {
                        $tagihan->status = 'lunas';
                    } else {
                        $tagihan->status = 'mencicil';
                    }
                    
                    $tagihan->save();

                    // Kirim WA Kuitansi Otomatis (Asinkron via Job Queue)
                    $student = $tagihan->student;
                    if ($student && $student->no_hp_wali) {
                        $nom = number_format($request->nominal, 0, ',', '.');
                        $sisa = number_format($tagihan->total_tagihan - $tagihan->total_dibayar, 0, ',', '.');
                        $statusTxt = strtoupper($tagihan->status);
                        
                        $pesan = "Halo Bapak/Ibu Wali Murid dari *{$student->nama}*,\n\n";
                        $pesan .= "Terima kasih, pembayaran *{$tagihan->nama_tagihan}* sebesar *Rp {$nom}* telah kami terima pada tanggal {$request->tanggal}.\n\n";
                        $pesan .= "Rincian Tagihan:\n";
                        $pesan .= "- Total Dibayar: Rp " . number_format($tagihan->total_dibayar, 0, ',', '.') . "\n";
                        $pesan .= "- Sisa Tanggungan: Rp {$sisa}\n";
                        $pesan .= "- Status: *{$statusTxt}*\n\n";
                        $pesan .= "Simpan pesan ini sebagai kuitansi digital Anda. Terima kasih.";

                        SendWhatsappMessageJob::dispatch($student->no_hp_wali, $pesan);
                    }
                }
            }
        });

        return redirect()->route('incomes.index')->with('success', 'Kas masuk berhasil dicatat dan tagihan diperbarui!')->with('show_receipt_id', $income ? $income->id : null);
    }

    public function getTagihanSiswa(string $student_id)
    {
        $activeYear = \App\Models\AcademicYear::getActive();
        $query = Tagihan::query()->where('student_id', $student_id)
                           ->whereIn('status', ['belum_bayar', 'mencicil']);
                           
        if ($activeYear) {
            $query->where('academic_year_id', $activeYear->id);
        }
        
        $tagihans = $query->get();
        
        return response()->json($tagihans);
    }
    
    public function history(Request $request)
    {
        $activeYear = \App\Models\AcademicYear::getActive();
        $type = $request->query('type', 'all'); // 'all', 'income', 'expense'
        $search = trim($request->query('search', ''));

        // Query Incomes
        $incomeQuery = Income::query()->with(['student', 'tagihan']);
        if ($activeYear) {
            $incomeQuery->where('academic_year_id', $activeYear->id);
        }
        if ($search !== '') {
            $incomeQuery->where(function($q) use ($search) {
                $q->where('jenis_pembayaran', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('student', function($sq) use ($search) {
                      $sq->where('nama', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                  });
            });
        }

        // Query Expenses
        $expenseQuery = \App\Models\Expense::query();
        if ($activeYear) {
            $expenseQuery->where('academic_year_id', $activeYear->id);
        }
        if ($search !== '') {
            $expenseQuery->where(function($q) use ($search) {
                $q->where('kategori', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        // Ringkasan Keuangan
        $totalIncome = (clone $incomeQuery)->sum('nominal');
        $totalExpense = (clone $expenseQuery)->sum('nominal');
        $netBalance = $totalIncome - $totalExpense;

        $items = collect();

        if ($type === 'income') {
            $items = $incomeQuery->latest('tanggal')->latest('id')->get()->map(function($item) {
                $item->transaction_type = 'income';
                return $item;
            });
        } elseif ($type === 'expense') {
            $items = $expenseQuery->latest('tanggal')->latest('id')->get()->map(function($item) {
                $item->transaction_type = 'expense';
                return $item;
            });
        } else {
            $incomes = $incomeQuery->latest('tanggal')->latest('id')->get()->map(function($item) {
                $item->transaction_type = 'income';
                return $item;
            });
            $expenses = $expenseQuery->latest('tanggal')->latest('id')->get()->map(function($item) {
                $item->transaction_type = 'expense';
                return $item;
            });

            $items = $incomes->concat($expenses)->sort(function($a, $b) {
                if ($a->tanggal === $b->tanggal) {
                    return $b->id <=> $a->id;
                }
                return strtotime($b->tanggal) <=> strtotime($a->tanggal);
            })->values();
        }

        $perPage = 15;
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $items->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $transactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            $items->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('incomes.history', compact('transactions', 'type', 'search', 'totalIncome', 'totalExpense', 'netBalance'));
    }

    public function destroy(Income $income)
    {
        if ($income->bukti) {
            Storage::disk('public')->delete($income->bukti);
        }
        
        Income::destroy($income->id);
        
        return redirect()->back()->with('success', 'Data kas masuk berhasil dihapus!');
    }
}
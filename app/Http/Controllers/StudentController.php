<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query();

        // Fitur Pencarian berdasarkan Nama atau NIS
        if ($request->filled('search')) {
            $search = addcslashes($request->search, '%_');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('nis', 'like', '%' . $search . '%');
            });
        }

        // Fitur Filter berdasarkan Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pagination 10 data per halaman
        $students = $query->latest()->paginate(10);
        
        $classes = Student::select('kelas')->distinct()->pluck('kelas')->filter();

        return view('students.index', compact('students', 'classes'));
    }

    public function promote(Request $request)
    {
        $request->validate([
            'kelas_asal' => 'required|string',
            'kelas_tujuan' => 'required|string',
        ]);

        $students = Student::where('kelas', $request->kelas_asal)->get();
        
        if ($students->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada siswa di kelas asal tersebut.');
        }

        DB::transaction(function () use ($students, $request) {
            foreach ($students as $student) {
                $student->kelas = $request->kelas_tujuan;
                
                // Jika Lulus, nonaktifkan (opsional)
                if (strtolower(trim($request->kelas_tujuan)) === 'lulus') {
                    $student->status = 'tidak_aktif';
                }
                
                $student->save();

                // Jika reset tagihan dicentang, hapus semua tagihan saat ini
                if ($request->filled('reset_tagihan')) {
                    \App\Models\Tagihan::where('student_id', $student->id)->delete();
                }
            }
        });

        return redirect()->back()->with('success', count($students) . ' siswa berhasil dipindah ke kelas ' . $request->kelas_tujuan . '.');
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|unique:students,nis|max:20',
            'nama' => 'required|string|max:255',
            'kelas' => 'required|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'nullable|string',
            'no_hp_wali' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,tidak_aktif',
        ], [
            'nis.unique' => 'NIS/NISN sudah terdaftar!',
        ]);

        DB::transaction(function () use ($validated) {
            $student = Student::create($validated);

            User::updateOrCreate(
                ['student_id' => $student->id],
                [
                    'name'       => $student->nama,
                    'email'      => $student->nis . '@wali.com',
                    'password'   => Hash::make($student->nis),
                    'role'       => 'wali',
                ]
            );
        });

        return redirect()->route('students.index')->with('success', 'Siswa & Akun Wali berhasil dibuat!');
    }

    public function resetWaliPassword(Student $student)
    {
        $user = User::where('student_id', $student->id)->first();
        if (!$user) {
            User::create([
                'student_id' => $student->id,
                'name'       => $student->nama,
                'email'      => $student->nis . '@wali.com',
                'password'   => Hash::make($student->nis),
                'role'       => 'wali',
            ]);
        } else {
            $user->update([
                'password' => Hash::make($student->nis)
            ]);
        }

        return redirect()->back()->with('success', "Password wali untuk {$student->nama} berhasil direset ke NISN ({$student->nis})!");
    }

    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'nis' => 'required|max:20|unique:students,nis,' . $student->id,
            'nama' => 'required|string|max:255',
            'kelas' => 'required|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'nullable|string',
            'no_hp_wali' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,tidak_aktif',
        ], [
            'nis.unique' => 'NIS/NISN sudah terdaftar untuk siswa lain!',
        ]);

        $student->update($request->all());

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function destroy(Student $student)
    {
        Student::destroy($student->id);
        
        return redirect()->route('students.index')->with('success', 'Data siswa berhasil dihapus!');
    }

    public function destroyAll()
    {
        Student::query()->delete();
        
        return redirect()->route('students.index')->with('success', 'Seluruh data siswa berhasil dihapus secara permanen!');
    }
    
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|max:5120'
        ], [
            'file_excel.required' => 'Pilih file Excel / CSV terlebih dahulu!',
        ]);

        $file = $request->file('file_excel');
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['xlsx', 'xls', 'csv', 'txt'])) {
            return redirect()->back()->with('error', 'Format file harus berupa .xlsx, .xls, atau .csv');
        }

        try {
            $delimiter = ';';
            if (in_array($extension, ['csv', 'txt'])) {
                $delimiter = $this->detectCsvDelimiter($file->getRealPath());
            }

            $import = new StudentsImport($delimiter);
            Excel::import($import, $file);

            $msg = [];
            if ($import->createdCount > 0) {
                $msg[] = "{$import->createdCount} data siswa baru ditambahkan";
            }
            if ($import->updatedCount > 0) {
                $msg[] = "{$import->updatedCount} data siswa diperbarui";
            }

            if (empty($msg)) {
                return redirect()->route('students.index')->with('warning', 'Tidak ada data siswa baru atau perubahan yang diimpor. Pastikan file sesuai template.');
            }

            return redirect()->route('students.index')->with('success', 'Berhasil mengimpor data siswa: ' . implode(', ', $msg) . '.');
        } catch (\Exception $e) {
            return redirect()->route('students.index')->with('error', 'Gagal impor data. ' . $e->getMessage());
        }
    }

    private function detectCsvDelimiter(string $filePath): string
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return ';';
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return ';';
        }

        $firstLine = fgets($handle);
        fclose($handle);

        if (!$firstLine) {
            return ';';
        }

        $semicolons = substr_count($firstLine, ';');
        $commas = substr_count($firstLine, ',');
        $tabs = substr_count($firstLine, "\t");

        if ($semicolons > $commas && $semicolons > $tabs) {
            return ';';
        }
        if ($commas > $semicolons && $commas > $tabs) {
            return ',';
        }
        if ($tabs > $semicolons && $tabs > $commas) {
            return "\t";
        }

        return ';';
    }

    public function downloadTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=template_import_siswa.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['nis', 'nama', 'kelas', 'jenis_kelamin', 'alamat', 'no_hp_wali', 'status'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');

            // Header BOM UTF-8 agar Excel membaca karakter khusus & format kolom dengan benar
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Gunakan titik koma (;) sebagai delimiter agar otomatis terpisah per-kolom di Excel Windows/Locale Indo
            fputcsv($file, $columns, ';');
            
            // Baris contoh
            fputcsv($file, ['250101', 'AHMAD EVAN AFANDI', '7', 'laki', 'malang', '08523456789', 'aktif'], ';');
            fputcsv($file, ['250102', 'CHOIRUL MUHAMMAD ILYAS', '7', 'laki', 'malang', '08523456790', 'aktif'], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\Income;
use Illuminate\Http\Request;

class TunggakanController extends Controller
{
    public function index(Request $request)
    {
        $activeYear = AcademicYear::getActive();
        if (!$activeYear) {
            return redirect()->route('academic-years.index')->with('error', 'Silakan aktifkan Tahun Ajaran terlebih dahulu.');
        }

        // Ambil tagihan yang BUKAN tahun aktif dan statusnya belum lunas
        $tagihans = Tagihan::with(['student', 'academicYear'])
            ->where('academic_year_id', '!=', $activeYear->id)
            ->whereIn('status', ['belum_bayar', 'mencicil'])
            ->get();

        // Kelompokkan berdasarkan siswa
        $tunggakanSiswa = [];
        $totalTunggakanKeseluruhan = 0;

        foreach ($tagihans as $t) {
            if (!$t->student) continue;

            $sisa = $t->total_tagihan - $t->total_dibayar;
            $totalTunggakanKeseluruhan += $sisa;

            $studentId = $t->student_id;
            if (!isset($tunggakanSiswa[$studentId])) {
                $tunggakanSiswa[$studentId] = [
                    'student' => $t->student,
                    'total_tunggakan' => 0,
                    'tagihans' => []
                ];
            }

            $tunggakanSiswa[$studentId]['total_tunggakan'] += $sisa;
            $tunggakanSiswa[$studentId]['tagihans'][] = $t;
        }

        return view('tunggakan.index', compact('tunggakanSiswa', 'totalTunggakanKeseluruhan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tagihan_id' => 'required|exists:tagihans,id',
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string'
        ]);

        $tagihan = Tagihan::findOrFail($request->tagihan_id);
        $activeYear = AcademicYear::getActive();

        // Buat record Income di tahun aktif saat ini (uang diterima hari ini)
        Income::create([
            'academic_year_id' => $activeYear ? $activeYear->id : null,
            'student_id' => $tagihan->student_id,
            'tagihan_id' => $tagihan->id,
            'tanggal' => date('Y-m-d'),
            'jenis_pembayaran' => 'Pelunasan Tunggakan: ' . $tagihan->nama_tagihan . ' (' . ($tagihan->academicYear ? $tagihan->academicYear->name : 'Tahun Lama') . ')',
            'nominal' => $request->nominal,
            'keterangan' => $request->keterangan
        ]);

        // Update nominal dibayar pada tagihan
        $tagihan->total_dibayar += $request->nominal;
        
        if ($tagihan->total_dibayar >= $tagihan->total_tagihan) {
            $tagihan->status = 'lunas';
        } else {
            $tagihan->status = 'mencicil';
        }
        $tagihan->save();

        // Kiri WA Kuitansi Otomatis
        $student = $tagihan->student;
        if ($student && $student->no_hp_wali) {
            $nom = number_format($request->nominal, 0, ',', '.');
            $sisa = number_format($tagihan->total_tagihan - $tagihan->total_dibayar, 0, ',', '.');
            $statusTxt = strtoupper($tagihan->status);
            $tahunLama = $tagihan->academicYear ? $tagihan->academicYear->name : 'Tahun Lama';
            
            $pesan = "Halo Bapak/Ibu Wali Murid dari *{$student->nama}*,\n\n";
            $pesan .= "Terima kasih, pembayaran TUNGGAKAN *{$tagihan->nama_tagihan}* (TA: {$tahunLama}) sebesar *Rp {$nom}* telah kami terima.\n\n";
            $pesan .= "Rincian Tagihan:\n";
            $pesan .= "- Total Dibayar: Rp " . number_format($tagihan->total_dibayar, 0, ',', '.') . "\n";
            $pesan .= "- Sisa Tanggungan: Rp {$sisa}\n";
            $pesan .= "- Status: *{$statusTxt}*\n\n";
            $pesan .= "Simpan pesan ini sebagai kuitansi digital Anda. Terima kasih.";

            \App\Services\FonnteService::sendMessage($student->no_hp_wali, $pesan);
        }

        return redirect()->back()->with('success', 'Pembayaran tunggakan berhasil dicatat!');
    }

    public function broadcast(Request $request)
    {
        $activeYear = AcademicYear::getActive();
        // Ambil semua tagihan tertunggak
        $tagihans = Tagihan::with(['student', 'academicYear'])
            ->where('academic_year_id', '!=', $activeYear ? $activeYear->id : null)
            ->whereIn('status', ['belum_bayar', 'mencicil'])
            ->get();

        // Kelompokkan sisa tagihan berdasarkan siswa
        $tunggakanSiswa = [];
        foreach ($tagihans as $t) {
            if (!$t->student || !$t->student->no_hp_wali) continue;

            $sisa = $t->total_tagihan - $t->total_dibayar;
            $studentId = $t->student_id;
            
            if (!isset($tunggakanSiswa[$studentId])) {
                $tunggakanSiswa[$studentId] = [
                    'hp' => $t->student->no_hp_wali,
                    'nama' => $t->student->nama,
                    'total' => 0,
                    'rincian' => []
                ];
            }

            $tunggakanSiswa[$studentId]['total'] += $sisa;
            $tahun = $t->academicYear ? $t->academicYear->name : '-';
            $tunggakanSiswa[$studentId]['rincian'][] = "- {$t->nama_tagihan} (TA: {$tahun}) : Rp " . number_format($sisa, 0, ',', '.');
        }

        $sentCount = 0;
        foreach ($tunggakanSiswa as $data) {
            $pesan = "Halo Bapak/Ibu Wali Murid dari *{$data['nama']}*,\n\n";
            $pesan .= "Bersama pesan ini kami menginformasikan bahwa masih terdapat *Tunggakan Biaya Sekolah* dari tahun ajaran sebelumnya dengan rincian sebagai berikut:\n\n";
            $pesan .= implode("\n", $data['rincian']) . "\n\n";
            $pesan .= "Total Tunggakan: *Rp " . number_format($data['total'], 0, ',', '.') . "*\n\n";
            $pesan .= "Mohon untuk dapat segera diselesaikan. Apabila sudah melakukan pembayaran, mohon abaikan pesan ini. Terima kasih.";

            \App\Services\FonnteService::sendMessage($data['hp'], $pesan);
            $sentCount++;
        }

        return redirect()->back()->with('success', "Pesan peringatan berhasil dikirim ke {$sentCount} wali murid!");
    }
}

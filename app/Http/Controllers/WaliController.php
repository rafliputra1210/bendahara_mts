<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class WaliController extends Controller
{
    public function dashboard()
    {
        // Ambil data siswa yang terhubung dengan akun wali yang sedang login
        $student = Auth::user()->student; 
        
        // Ambil riwayat pembayaran (kas masuk) terbaru
        $incomes = $student->incomes()->latest('tanggal')->get();
        
        // Ambil daftar tagihan
        $tagihans = $student->tagihans()->orderBy('urutan', 'asc')->get();

        // Hitung Statistik Keuangan Siswa
        $totalTagihan = $tagihans->sum('total_tagihan');
        $totalDibayar = $tagihans->sum('total_dibayar');
        $sisaTunggakan = $totalTagihan - $totalDibayar;

        // Hitung persentase pelunasan (mencegah pembagian dengan 0)
        $persentase = $totalTagihan > 0 ? round(($totalDibayar / $totalTagihan) * 100) : 0;

        return view('wali.dashboard', compact(
            'student', 
            'incomes', 
            'tagihans', 
            'totalTagihan', 
            'totalDibayar', 
            'sisaTunggakan',
            'persentase'
        ));
    }
}
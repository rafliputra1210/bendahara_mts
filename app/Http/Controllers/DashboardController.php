<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Income;
use App\Models\Expense;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung total data
        // Tambahkan ->query() setelah pemanggilan Model
        $totalSiswa = Student::query()->where('status', 'aktif')->count();
        $totalPemasukan = Income::query()->sum('nominal');
        $totalPengeluaran = Expense::query()->sum('nominal');
        
        // Kalkulasi Saldo
        $saldoKas = $totalPemasukan - $totalPengeluaran;

        // Ambil 5 transaksi pemasukan terbaru untuk tabel di dashboard
        $transaksiTerbaru = Income::query()->with('student')->latest('tanggal')->take(5)->get();

        return view('dashboard', compact(
            'totalSiswa', 
            'totalPemasukan', 
            'totalPengeluaran', 
            'saldoKas', 
            'transaksiTerbaru'
        ));
    }
}
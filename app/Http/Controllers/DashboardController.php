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
        $activeYear = \App\Models\AcademicYear::getActive();
        $yearId = $activeYear ? $activeYear->id : null;

        $totalSiswa = Student::query()->where('status', 'aktif')->count();
        $totalPemasukan = Income::query()->when($yearId, function($q) use ($yearId) {
            return $q->where('academic_year_id', $yearId);
        })->sum('nominal');
        $totalPengeluaran = Expense::query()->when($yearId, function($q) use ($yearId) {
            return $q->where('academic_year_id', $yearId);
        })->sum('nominal');
        
        // Kalkulasi Saldo
        $saldoKas = $totalPemasukan - $totalPengeluaran;

        // Ambil 5 transaksi pemasukan terbaru untuk tabel di dashboard
        $transaksiTerbaru = Income::query()->with('student')
            ->when($yearId, function($q) use ($yearId) {
                return $q->where('academic_year_id', $yearId);
            })->latest('tanggal')->take(5)->get();

        return view('dashboard', compact(
            'totalSiswa', 
            'totalPemasukan', 
            'totalPengeluaran', 
            'saldoKas', 
            'transaksiTerbaru'
        ));
    }
}
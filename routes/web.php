<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return view('welcome');
});

// Middleware untuk Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Middleware untuk Dashboard dan Data Siswa (Digabungkan agar tidak duplikat)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Route Data Siswa
    Route::resource('students', StudentController::class);
}); // <-- Di sini letak kurangnya penutup kurung pada kode asli Anda

// Route untuk Kas Masuk
Route::resource('incomes', IncomeController::class);

// Route untuk Pengeluaran
Route::resource('expenses', ExpenseController::class);

// Route for Reports
Route::resource('reports', ReportController::class);

Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
Route::get('reports/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
require __DIR__.'/auth.php';
<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return redirect()->route('login');
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

    // Route untuk Kas Masuk
    Route::post('incomes/target-pembayaran', [IncomeController::class, 'updateTargetPembayaran'])->name('incomes.update-target');
    Route::get('incomes/create-other', [IncomeController::class, 'createOther'])->name('incomes.create_other');
    Route::get('incomes/history', [IncomeController::class, 'history'])->name('incomes.history');
    Route::resource('incomes', IncomeController::class);

    // Route untuk Pengeluaran
    Route::resource('expenses', ExpenseController::class);

    // Route for Reports
    Route::get('reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
    Route::get('reports/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

    // Route untuk Tunggakan
    Route::get('tunggakan', [App\Http\Controllers\TunggakanController::class, 'index'])->name('tunggakan.index');
    Route::post('tunggakan/bayar', [App\Http\Controllers\TunggakanController::class, 'store'])->name('tunggakan.store');
    Route::post('tunggakan/broadcast', [App\Http\Controllers\TunggakanController::class, 'broadcast'])->name('tunggakan.broadcast');

    // Route untuk AJAX Tagihan Siswa
    Route::get('/api/tagihan-siswa/{id}', [IncomeController::class, 'getTagihanSiswa']);
    
    // Route untuk Data Siswa
    // Route untuk Pengaturan Tahun Ajaran & WA
    Route::get('settings/academic-years', [App\Http\Controllers\AcademicYearController::class, 'index'])->name('academic-years.index');
    Route::post('settings/academic-years', [App\Http\Controllers\AcademicYearController::class, 'store'])->name('academic-years.store');
    Route::post('settings/academic-years/{academicYear}/set-active', [App\Http\Controllers\AcademicYearController::class, 'setActive'])->name('academic-years.set_active');
    Route::post('settings/wa-token', [App\Http\Controllers\AcademicYearController::class, 'saveWaToken'])->name('settings.wa_token');

    Route::post('students/import', [StudentController::class, 'importExcel'])->name('students.import');
    Route::get('students/template', [StudentController::class, 'downloadTemplate'])->name('students.template');
    Route::post('students/promote', [StudentController::class, 'promote'])->name('students.promote');
    Route::resource('students', StudentController::class);
    });

require __DIR__.'/auth.php';
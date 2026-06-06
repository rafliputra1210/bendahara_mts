<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\LaporanKeuanganExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Set default ke bulan ini jika tidak ada filter
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->format('Y-m-d');

        $transactions = $this->getTransactions($startDate, $endDate);

        return view('reports.index', compact('transactions', 'startDate', 'endDate'));
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $transactions = $this->getTransactions($startDate, $endDate);

        $pdf = Pdf::loadView('reports.pdf', compact('transactions', 'startDate', 'endDate'));
        
        return $pdf->download('Laporan_Keuangan_' . $startDate . '_sd_' . $endDate . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        
        return Excel::download(new LaporanKeuanganExport($startDate, $endDate), 'Laporan_Keuangan_' . $startDate . '_sd_' . $endDate . '.xlsx');
    }

    private function getTransactions(string $startDate, string $endDate)
    {
        $activeYear = \App\Models\AcademicYear::getActive();
        $yearId = $activeYear ? $activeYear->id : null;

        // 2. Tambahkan query() dan lengkapi parameter whereBetween
        $incomes = Income::query()->with('student')
            ->when($yearId, function($q) use ($yearId) {
                return $q->where('academic_year_id', $yearId);
            })
            ->whereBetween('tanggal', [$startDate, $endDate], 'and', false)
            ->get()
            ->map(function ($item) {
                $item->tipe = 'masuk';
                $item->deskripsi = $item->jenis_pembayaran . ($item->student ? ' - ' . $item->student->nama : '');
                return $item;
            });

        // 3. Tambahkan query() dan lengkapi parameter whereBetween
        $expenses = Expense::query()
            ->when($yearId, function($q) use ($yearId) {
                return $q->where('academic_year_id', $yearId);
            })
            ->whereBetween('tanggal', [$startDate, $endDate], 'and', false)
            ->get()
            ->map(function ($item) {
                $item->tipe = 'keluar';
                $item->deskripsi = $item->kategori . ' - ' . $item->keterangan;
                return $item;
            });

        // Gabungkan dan urutkan berdasarkan tanggal
        return $incomes->concat($expenses)->sortBy('tanggal');
    }
}
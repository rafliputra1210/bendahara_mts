<?php

namespace App\Exports;

use App\Models\Income;
use App\Models\Expense;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanKeuanganExport implements FromView, ShouldAutoSize
{
    protected string $startDate;
    protected string $endDate;

    public function __construct(string $startDate, string $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        $incomes = Income::query()->with('student')
            // Tambahkan parameter 'and' dan false agar Intelephense tidak error
            ->whereBetween('tanggal', [$this->startDate, $this->endDate], 'and', false)
            ->get()
            ->map(function ($item) {
                $item->tipe = 'masuk';
                $item->deskripsi = $item->jenis_pembayaran . ($item->student ? ' - ' . $item->student->nama : '');
                return $item;
            });

        $expenses = Expense::query()
            // Tambahkan parameter 'and' dan false di sini juga
            ->whereBetween('tanggal', [$this->startDate, $this->endDate], 'and', false)
            ->get()
            ->map(function ($item) {
                $item->tipe = 'keluar';
                $item->deskripsi = $item->kategori . ' - ' . $item->keterangan;
                return $item;
            });

        $transactions = $incomes->concat($expenses)->sortBy('tanggal');

        return view('reports.excel', [
            'transactions' => $transactions,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index()
    {
        $activeYear = \App\Models\AcademicYear::getActive();
        $query = Expense::query();
        if ($activeYear) {
            $query->where('academic_year_id', $activeYear->id);
        }
        $expenses = $query->latest('tanggal')->paginate(10);
        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'kategori' => 'required|string',
            'nominal' => 'required|numeric|min:0',
            'bukti' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'keterangan' => 'nullable|string',
        ]);

        if ($request->hasFile('bukti')) {
            $validated['bukti'] = $request->file('bukti')->store('bukti_pengeluaran', 'public');
        }

        $activeYear = \App\Models\AcademicYear::getActive();
        if ($activeYear) {
            $validated['academic_year_id'] = $activeYear->id;
        }

        Expense::query()->create($validated);

        return redirect()->route('expenses.index')->with('success', 'Data pengeluaran berhasil dicatat!');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->bukti) {
            Storage::disk('public')->delete($expense->bukti);
        }
        
        // Hapus kode ini: $expense->delete();
        // Ganti menjadi:
        Expense::destroy($expense->id);
        
        return redirect()->route('expenses.index')->with('success', 'Data pengeluaran berhasil dihapus!');
    }
}
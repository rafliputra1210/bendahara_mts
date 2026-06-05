<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index()
    {
        // Tambahkan query() agar VS Code mengenali method latest()
        $expenses = Expense::query()->latest('tanggal')->paginate(10);
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

        // Tambahkan query() agar VS Code mengenali method create()
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
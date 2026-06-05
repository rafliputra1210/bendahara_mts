<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IncomeController extends Controller
{
    public function index()
    {
        // Tambahkan query() agar VS Code mengenali method with()
        $incomes = Income::query()->with('student')->latest('tanggal')->paginate(10);
        return view('incomes.index', compact('incomes'));
    }

    public function create()
    {
        // Tambahkan query() agar VS Code mengenali method where()
        $students = Student::query()->where('status', 'aktif')->orderBy('nama', 'asc')->get();
        return view('incomes.create', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'tanggal' => 'required|date',
            'jenis_pembayaran' => 'required|string',
            'nominal' => 'required|numeric|min:0',
            'bukti' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'keterangan' => 'nullable|string',
        ]);

        if ($request->hasFile('bukti')) {
            $validated['bukti'] = $request->file('bukti')->store('bukti_pemasukan', 'public');
        }

        // Tambahkan query() agar VS Code mengenali method create()
        Income::query()->create($validated);

        return redirect()->route('incomes.index')->with('success', 'Data kas masuk berhasil dicatat!');
    }

    public function destroy(Income $income)
    {
        if ($income->bukti) {
            Storage::disk('public')->delete($income->bukti);
        }

        // Hapus kode ini: $income->delete();
        // Ganti menjadi:
        Income::destroy($income->id);
        
        return redirect()->route('incomes.index')->with('success', 'Data kas masuk berhasil dihapus!');
    }
}
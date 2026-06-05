<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query();

        // Fitur Pencarian berdasarkan Nama atau NIS
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('nis', 'like', '%' . $request->search . '%');
        }

        // Fitur Filter berdasarkan Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pagination 10 data per halaman
        $students = $query->latest()->paginate(10);

        return view('students.index', compact('students'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|unique:students,nis|max:20',
            'nama' => 'required|string|max:255',
            'kelas' => 'required|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'nullable|string',
            'no_hp_wali' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,tidak_aktif',
        ], [
            'nis.unique' => 'NIS/NISN sudah terdaftar!',
        ]);

        // Tambahkan query() agar VS Code mengenali method create()
        Student::query()->create($request->all());

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil ditambahkan!');
    }

    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'nis' => 'required|max:20|unique:students,nis,' . $student->id,
            'nama' => 'required|string|max:255',
            'kelas' => 'required|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'nullable|string',
            'no_hp_wali' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,tidak_aktif',
        ], [
            'nis.unique' => 'NIS/NISN sudah terdaftar untuk siswa lain!',
        ]);

        $student->update($request->all());

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function destroy(Student $student)
    {
        // Hapus kode ini: $student->delete();
        // Ganti menjadi:
        Student::destroy($student->id);
        
        return redirect()->route('students.index')->with('success', 'Data siswa berhasil dihapus!');
    }
}
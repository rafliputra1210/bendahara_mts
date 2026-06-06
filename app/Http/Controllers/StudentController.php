<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport; // <-- Tambahkan baris ini

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
        
        $classes = Student::select('kelas')->distinct()->pluck('kelas')->filter();

        return view('students.index', compact('students', 'classes'));
    }

    public function promote(Request $request)
    {
        $request->validate([
            'kelas_asal' => 'required|string',
            'kelas_tujuan' => 'required|string',
        ]);

        $students = Student::where('kelas', $request->kelas_asal)->get();
        
        if ($students->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada siswa di kelas asal tersebut.');
        }

        foreach ($students as $student) {
            $student->kelas = $request->kelas_tujuan;
            
            // Jika Lulus, nonaktifkan (opsional)
            if (strtolower(trim($request->kelas_tujuan)) === 'lulus') {
                $student->status = 'tidak_aktif';
            }
            
            $student->save();

            // Jika reset tagihan dicentang, hapus semua tagihan saat ini
            if ($request->filled('reset_tagihan')) {
                \App\Models\Tagihan::where('student_id', $student->id)->delete();
            }
        }

        return redirect()->back()->with('success', count($students) . ' siswa berhasil dipindah ke kelas ' . $request->kelas_tujuan . '.');
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
        Student::destroy($student->id);
        
        return redirect()->route('students.index')->with('success', 'Data siswa berhasil dihapus!');
    }
    
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048'
        ], [
            'file_excel.required' => 'Pilih file Excel terlebih dahulu!',
            'file_excel.mimes' => 'Format file harus berupa .xlsx, .xls, atau .csv',
        ]);

        try {
            Excel::import(new StudentsImport, $request->file('file_excel'));
            return redirect()->route('students.index')->with('success', 'Ribuan data siswa berhasil diimpor!');
        } catch (\Exception $e) {
            return redirect()->route('students.index')->with('error', 'Gagal impor data. Pastikan format Excel sesuai. Error: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=template_import_siswa.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['nis', 'nama', 'kelas', 'jenis_kelamin', 'alamat', 'no_hp_wali', 'status'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            // Berikan satu baris contoh
            fputcsv($file, ['123456', 'Ahmad Fauzi', '10A', 'L', 'Jl. Sudirman No 1', '081234567890', 'aktif']);
            fputcsv($file, ['123457', 'Siti Aminah', '10B', 'P', 'Jl. Merdeka No 2', '089876543210', 'aktif']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
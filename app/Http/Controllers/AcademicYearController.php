<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassTarget;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Tagihan;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        // Setup initial migration if no academic years exist
        if (AcademicYear::count() === 0) {
            $this->runInitialSetup();
        }

        $academicYears = AcademicYear::latest()->get();
        $waToken = \App\Services\FonnteService::getToken();
        
        return view('settings.academic_years', compact('academicYears', 'waToken'));
    }

    public function saveWaToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        \App\Services\FonnteService::saveToken($request->token);
        
        return redirect()->back()->with('success', 'Token WhatsApp Gateway berhasil disimpan!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:academic_years,name'
        ]);

        $year = AcademicYear::create([
            'name' => $request->name,
            'is_active' => false
        ]);

        return redirect()->back()->with('success', 'Tahun Ajaran berhasil ditambahkan.');
    }

    public function setActive(AcademicYear $academicYear)
    {
        // Deactivate all
        AcademicYear::where('is_active', true)->update(['is_active' => false]);
        
        // Activate selected
        $academicYear->update(['is_active' => true]);

        return redirect()->back()->with('success', "Tahun Ajaran {$academicYear->name} sekarang aktif.");
    }

    private function runInitialSetup()
    {
        $year = AcademicYear::create([
            'name' => '2025/2026',
            'is_active' => true
        ]);

        // Migrate existing orphaned transactions
        Tagihan::whereNull('academic_year_id')->update(['academic_year_id' => $year->id]);
        Income::whereNull('academic_year_id')->update(['academic_year_id' => $year->id]);
        Expense::whereNull('academic_year_id')->update(['academic_year_id' => $year->id]);

        // Migrate json targets to DB
        $configPath = storage_path('app/public/class_targets.json');
        if (file_exists($configPath)) {
            $targets = json_decode(file_get_contents($configPath), true) ?: [];
            foreach ($targets as $kelas => $slots) {
                foreach ($slots as $urutan => $data) {
                    ClassTarget::updateOrCreate(
                        ['academic_year_id' => $year->id, 'kelas' => $kelas, 'urutan' => $urutan],
                        ['nama_tagihan' => $data['name'], 'nominal' => $data['nominal']]
                    );
                }
            }
        }
    }
}

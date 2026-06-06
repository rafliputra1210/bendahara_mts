<?php
use App\Models\AcademicYear;
use App\Models\Tagihan;
use App\Models\Income;
use App\Models\Expense;
use App\Models\ClassTarget;

$year = AcademicYear::firstOrCreate(
    ['name' => '2025/2026'],
    ['is_active' => true]
);

Tagihan::whereNull('academic_year_id')->update(['academic_year_id' => $year->id]);
Income::whereNull('academic_year_id')->update(['academic_year_id' => $year->id]);
Expense::whereNull('academic_year_id')->update(['academic_year_id' => $year->id]);

// Migrate json to DB
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
echo "Setup complete.\n";

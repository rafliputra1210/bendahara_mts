<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat Akun Admin
        User::create([
            'name' => 'Administrator Utama',
            'email' => 'admin@bendahara.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // 2. Buat Akun Bendahara
        User::create([
            'name' => 'Bendahara Pondok',
            'email' => 'bendahara@bendahara.com',
            'password' => Hash::make('password123'),
            'role' => 'bendahara',
        ]);

        // 3. Buat Akun Kepala Sekolah / Pengasuh
        User::create([
            'name' => 'Kepala Sekolah / Pengasuh',
            'email' => 'kepsek@bendahara.com',
            'password' => Hash::make('password123'),
            'role' => 'kepsek',
        ]);

        // 4. Buat Data Siswa Dummy untuk Testing
        $siswaData = [
            ['nis' => '10001', 'nama' => 'Ahmad Fauzi', 'kelas' => '10A', 'jenis_kelamin' => 'L', 'alamat' => 'Jombang', 'no_hp_wali' => '081234567890'],
            ['nis' => '10002', 'nama' => 'Siti Aminah', 'kelas' => '10A', 'jenis_kelamin' => 'P', 'alamat' => 'Malang', 'no_hp_wali' => '081234567891'],
            ['nis' => '10003', 'nama' => 'Muhammad Rizky', 'kelas' => '11B', 'jenis_kelamin' => 'L', 'alamat' => 'Mojokerto', 'no_hp_wali' => '081234567892'],
            ['nis' => '10004', 'nama' => 'Lailatul Qodriyah', 'kelas' => '11B', 'jenis_kelamin' => 'P', 'alamat' => 'Surabaya', 'no_hp_wali' => '081234567893'],
            ['nis' => '10005', 'nama' => 'Zainal Abidin', 'kelas' => '12C', 'jenis_kelamin' => 'L', 'alamat' => 'Pasuruan', 'no_hp_wali' => '081234567894'],
        ];

        foreach ($siswaData as $siswa) {
            Student::create($siswa);
        }
    }
}
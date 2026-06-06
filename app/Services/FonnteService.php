<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    /**
     * Mengirim pesan teks biasa.
     */
    public static function sendMessage($target, $message)
    {
        // Ambil token dari file JSON atau database
        $token = self::getToken();

        if (!$token) {
            Log::warning('Fonnte Token tidak ditemukan. Gagal mengirim WA.');
            return false;
        }

        // Hapus karakter non-numerik pada nomor HP, ubah 08 ke 628
        $target = preg_replace('/[^0-9]/', '', $target);
        if (substr($target, 0, 1) === '0') {
            $target = '62' . substr($target, 1);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62', // Default Indonesia
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Fonnte API Error: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('Fonnte API Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mengambil token dari JSON.
     */
    public static function getToken()
    {
        $configPath = storage_path('app/public/wa_config.json');
        if (file_exists($configPath)) {
            $data = json_decode(file_get_contents($configPath), true);
            return $data['token'] ?? null;
        }
        return env('FONNTE_TOKEN', null);
    }

    /**
     * Menyimpan token ke JSON.
     */
    public static function saveToken($token)
    {
        $configPath = storage_path('app/public/wa_config.json');
        file_put_contents($configPath, json_encode(['token' => $token]));
    }
}

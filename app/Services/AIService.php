<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    public function getRekomendasi($jenis_jabatan, $jenis_peserta, $nama, $kategori, $persentase)
    {
        if ($jenis_jabatan === 1) {
            $jabatan = 'Jabatan Pelaksana atau Jabatan Fungsional Terampil';
        } else if ($jenis_jabatan === 2) {
            $jabatan = 'Jabatan Pengawas (Jabatan Struktural) atau Jabatan Fungsional Ahli Pertama';
        } else if ($jenis_jabatan === 3) {
            $jabatan = 'Jabatan Administrator (Jabatan Struktural) atau Jabatan Fungsional Ahli Muda';
        } else if ($jenis_jabatan === 4) {
            $jabatan = 'Jabatan Pimpinan Tinggi Pratama (Jabatan Struktural) atau Jabatan Fungsional Ahli Madya';
        }

        $context = $jenis_peserta === 'ASN'
            ? "di lingkungan pemerintahan (ASN), sesuai dengan jabatan yang bersangkutan yaitu {$jabatan} di instansi daerah."
            : "di sektor non-ASN seperti BUMN, BUMD, atau swasta, termasuk jabatan analis, manajer, atau spesialis.";

        $prompt = "
            Anda adalah seorang pakar pengembangan SDM untuk pegawai non-ASN jika pesertanya adalah seorang non-ASN dan anda adalah juga seorang Assessor Ahli Utama
            di pemerintahan yang sangat profesional dan memiliki pengetahuan luas tentang pengembangan karir dan potensi ASN jika pesertanya adalah seorang ASN.
            Peserta bernama {$nama} mendapatkan skor Job Person Match (JPM) sebesar {$persentase}% dengan kategori '{$kategori}'.
            Berdasarkan hal tersebut, berikan:
            1. Rekomendasi pengembangan diri (pelatihan, kompetensi, atau peningkatan kemampuan).
            2. Saran potensi jabatan yang cocok {$context}

            Tuliskan dalam bentuk poin-poin singkat.
        ";

        // $response = Http::withToken(env('DEEPSEEK_API_KEY'))
        //     ->post('https://api.deepseek.com/chat/completions', [
        //         'model' => 'deepseek-chat',
        //         'messages' => [
        //             ['role' => 'user', 'content' => $prompt]
        //         ],
        //         'temperature' => 1.0,
        //         'stream' => false
        //     ]);
        $response = Http::withToken(env('OPENAPI_KEY'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                // 'max_tokens' => 100,
                'messages' => [
                    ['role' => 'system', 'content' => $prompt]
                ],
                'temperature' => 0.7,
            ]);

        if (!$response->successful()) {
            Log::error('API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return 'Gagal menghubungi AI. Coba lagi nanti.';
        }

        return $response->json()['choices'][0]['message']['content'] ?? 'Maaf, belum ada rekomendasi.';
    }
}

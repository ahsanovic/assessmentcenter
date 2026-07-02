<?php

namespace App\Services\Dokumen;

use App\Models\BeritaAcara;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Carbon\Carbon;
use League\CommonMark\GithubFlavoredMarkdownConverter;

class BeritaAcaraPdfService
{
    public function generateFromModel(BeritaAcara $beritaAcara): DomPdf
    {
        return $this->generate($beritaAcara->toPdfPayload());
    }

    /**
     * Bangun instance PDF berita acara dari payload form.
     *
     * @param  array<string, mixed>  $payload
     */
    public function generate(array $payload): DomPdf
    {
        $data = $this->normalize($payload);

        $view = ($data['mekanisme_penkom'] ?? 'tusi') === 'retribusi'
            ? 'pdf.dokumen.berita-acara-retribusi'
            : 'pdf.dokumen.berita-acara';

        return Pdf::loadView($view, $data)
            ->setPaper('A4', 'portrait');
    }

    /**
     * Nama file unduhan yang aman.
     */
    public function filename(string $judul, $event = null): string
    {
        $base = 'berita-acara';

        $judulSlug = preg_replace('/[^A-Za-z0-9]+/', '-', strtolower($judul));
        $judulSlug = trim($judulSlug, '-');

        $parts = array_filter([
            $base,
            $judulSlug ?: null,
            $event?->nama_event ? preg_replace('/[^A-Za-z0-9]+/', '-', strtolower($event->nama_event)) : null,
        ]);

        return implode('_', $parts).'.pdf';
    }

    /**
     * Normalisasi dan lengkapi data untuk template.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected function normalize(array $payload): array
    {
        $tanggal = ! empty($payload['tanggal']) ? Carbon::parse($payload['tanggal']) : null;

        $bulanId = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $hariId = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        $payload['mekanisme_penkom'] = $payload['mekanisme_penkom'] ?? 'tusi';
        $payload['hari'] = $payload['hari'] ?: ($tanggal ? ($hariId[$tanggal->format('l')] ?? '') : '');
        $payload['tanggal_angka'] = $tanggal?->format('d');
        $payload['bulan_teks'] = $tanggal ? ($bulanId[(int) $tanggal->format('n')] ?? '') : '';
        $payload['tahun'] = $tanggal?->format('Y') ?? date('Y');
        $payload['tanggal_lengkap'] = $tanggal ? $tanggal->format('d').' '.$payload['bulan_teks'].' '.$payload['tahun'] : '';

        $payload['tanggal_penyerahan_rekap_teks'] = $this->tanggalTeks($payload['tanggal_penyerahan_rekap'] ?? null, $bulanId);
        $payload['tanggal_penyerahan_laporan_teks'] = $this->tanggalTeks($payload['tanggal_penyerahan_laporan'] ?? null, $bulanId);

        $payload['catatan_html'] = $this->markdownToHtml($payload['catatan'] ?? null);
        $payload['nomor_tidak_hadir_html'] = $this->markdownToHtml($payload['nomor_tidak_hadir'] ?? null);
        $payload['alasan_tidak_hadir_html'] = $this->markdownToHtml($payload['alasan_tidak_hadir'] ?? null);

        return $payload;
    }

    /**
     * Ubah tanggal (Y-m-d) menjadi format "d Bulan Y".
     *
     * @param  array<int, string>  $bulanId
     */
    protected function tanggalTeks(?string $value, array $bulanId): ?string
    {
        if (blank($value)) {
            return null;
        }

        $date = Carbon::parse($value);

        return $date->format('d').' '.($bulanId[(int) $date->format('n')] ?? '').' '.$date->format('Y');
    }

    /**
     * Konversi teks Markdown menjadi HTML yang aman untuk PDF.
     */
    protected function markdownToHtml(?string $markdown): string
    {
        if (blank($markdown)) {
            return '';
        }

        $converter = new GithubFlavoredMarkdownConverter([
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
            'renderer' => [
                'soft_break' => "<br />\n",
            ],
        ]);

        return (string) $converter->convert($markdown);
    }
}

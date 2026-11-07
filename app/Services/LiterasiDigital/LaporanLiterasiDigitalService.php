<?php

namespace App\Services\LiterasiDigital;

use App\Models\LiterasiDigital\HasilLiterasiDigital;
use App\Models\LiterasiDigital\RefPilarLiterasiDigital;
use League\CommonMark\GithubFlavoredMarkdownConverter;

class LaporanLiterasiDigitalService
{
    /**
     * Standar tiap pilar, sama seperti kolom standar pada laporan PSPK level 3.
     * Empat pilar kali 3 menghasilkan 12, yaitu penyebut JPM.
     */
    public const STANDAR = 3;

    public function __construct(
        private DeskripsiLevelLiterasiDigitalService $deskripsi,
        private SaranPengembanganLiterasiDigitalService $saran,
    ) {}

    /**
     * @return array{
     *     baris: list<array{
     *         nama: string,
     *         deskripsi_pilar: ?string,
     *         standar: int,
     *         capaian: int,
     *         deskripsi: ?string,
     *         saran_html: string
     *     }>,
     *     total_capaian: int,
     *     total_standar: int,
     *     jpm: float,
     *     kategori: string
     * }
     */
    public function susun(int $eventId, HasilLiterasiDigital $hasil): array
    {
        $pilar = RefPilarLiterasiDigital::query()
            ->whereIn('kode', LiterasiDigitalUjianService::KODE_PILAR)
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        $baris = [];

        foreach ($pilar as $item) {
            $level = (int) $hasil->{'level_'.$item->kode};
            $teksSaran = $this->saran->teksUntuk($eventId, (int) $item->id, $level);

            $baris[] = [
                'nama' => $item->nama,
                'deskripsi_pilar' => $item->deskripsi,
                'standar' => self::STANDAR,
                'capaian' => $level,
                'deskripsi' => $this->deskripsi->teksUntuk($eventId, (int) $item->id, $level),
                'saran_html' => $this->markdownKeHtml($teksSaran),
            ];
        }

        return [
            'baris' => $baris,
            'total_capaian' => array_sum(array_column($baris, 'capaian')),
            'total_standar' => self::STANDAR * count($baris),
            'jpm' => (float) $hasil->jpm,
            'kategori' => (string) ($hasil->kategori ?? ''),
        ];
    }

    public function markdownKeHtml(?string $markdown): string
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

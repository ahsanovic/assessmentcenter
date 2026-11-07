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
     *         deskripsi_pecahan: list<string>,
     *         saran_html: string,
     *         saran_pecahan: list<string>
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

            $deskripsi = $this->deskripsi->teksUntuk($eventId, (int) $item->id, $level);

            $baris[] = [
                'nama' => $item->nama,
                'deskripsi_pilar' => $item->deskripsi,
                'standar' => self::STANDAR,
                'capaian' => $level,
                'deskripsi' => $deskripsi,
                'deskripsi_pecahan' => $this->pecahanTeks($deskripsi),
                'saran_html' => $this->markdownKeHtml($teksSaran),
                'saran_pecahan' => $this->pecahanMarkdown($teksSaran),
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

    /**
     * Pecah saran menjadi blok pendek. DomPDF hanya bisa ganti halaman di batas baris tabel,
     * jadi tiap judul atau butir menjadi satu baris yang utuh pindah ke halaman berikutnya.
     *
     * @return list<string>
     */
    public function pecahanMarkdown(?string $markdown): array
    {
        $html = trim($this->markdownKeHtml($markdown));
        if ($html === '') {
            return [];
        }

        $dom = new \DOMDocument;
        $sebelum = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8"><div id="akar">'.$html.'</div>');
        libxml_clear_errors();
        libxml_use_internal_errors($sebelum);

        $akar = (new \DOMXPath($dom))->query('//div[@id="akar"]')->item(0);
        if (! $akar instanceof \DOMElement) {
            return [$html];
        }

        $pecahan = [];
        foreach ($akar->childNodes as $anak) {
            foreach ($this->pecahanNode($anak) as $bagian) {
                $pecahan[] = $bagian;
            }
        }

        return $pecahan === [] ? [$html] : $pecahan;
    }

    /**
     * @return list<string>
     */
    public function pecahanTeks(?string $teks): array
    {
        $teks = trim((string) $teks);
        if ($teks === '') {
            return [];
        }

        $pecahan = [];
        foreach (preg_split("/\n+/", $teks) ?: [] as $baris) {
            $baris = trim($baris);
            if ($baris === '') {
                continue;
            }
            foreach ($this->potongTeks($baris) as $bagian) {
                $pecahan[] = $bagian;
            }
        }

        return $pecahan;
    }

    /**
     * @return list<string>
     */
    private function pecahanNode(\DOMNode $node): array
    {
        if ($node instanceof \DOMText) {
            $teks = trim($node->textContent);

            return $teks === '' ? [] : array_map(
                fn (string $bagian) => htmlspecialchars($bagian, ENT_QUOTES, 'UTF-8'),
                $this->potongTeks($teks)
            );
        }

        if (! $node instanceof \DOMElement) {
            return [];
        }

        $nama = strtolower($node->nodeName);
        if ($nama === 'ul' || $nama === 'ol') {
            return $this->pecahanDaftar($node, $nama);
        }

        return $this->pecahanHtmlPanjang($node->ownerDocument->saveHTML($node) ?: '');
    }

    /**
     * @return list<string>
     */
    private function pecahanDaftar(\DOMElement $daftar, string $nama): array
    {
        $pecahan = [];
        $nomor = (int) $daftar->getAttribute('start');
        if ($nomor < 1) {
            $nomor = 1;
        }

        foreach ($daftar->childNodes as $anak) {
            if (! $anak instanceof \DOMElement || strtolower($anak->nodeName) !== 'li') {
                continue;
            }

            $isi = '';
            foreach ($anak->childNodes as $cucu) {
                $isi .= $anak->ownerDocument->saveHTML($cucu);
            }
            if (trim(strip_tags($isi)) === '') {
                continue;
            }

            $awal = $nama === 'ol' ? ' start="'.$nomor.'"' : '';
            foreach ($this->pecahanHtmlPanjang($isi) as $indeks => $bagian) {
                $pecahan[] = $indeks === 0
                    ? '<'.$nama.$awal.'><li>'.$bagian.'</li></'.$nama.'>'
                    : '<p>'.$bagian.'</p>';
            }
            $nomor++;
        }

        return $pecahan;
    }

    /**
     * @return list<string>
     */
    private function pecahanHtmlPanjang(string $html): array
    {
        $html = trim($html);
        if ($html === '') {
            return [];
        }

        $polos = trim(preg_replace('/\s+/', ' ', strip_tags($html)) ?? '');
        if (mb_strlen($polos) <= 600) {
            return [$html];
        }

        $bagian = preg_split('/<br\s*\/?\s*>/i', $html) ?: [$html];
        if (count($bagian) < 2) {
            return [$html];
        }

        $grup = [];
        $wadah = '';
        $panjang = 0;
        foreach ($bagian as $baris) {
            $panjangBaris = mb_strlen(trim(strip_tags($baris)));
            if ($wadah !== '' && $panjang + $panjangBaris > 500) {
                $grup[] = $wadah;
                $wadah = $baris;
                $panjang = $panjangBaris;
            } else {
                $wadah = $wadah === '' ? $baris : $wadah.'<br />'.$baris;
                $panjang += $panjangBaris;
            }
        }

        if (trim(strip_tags($wadah)) !== '') {
            $grup[] = $wadah;
        }

        return $grup === [] ? [$html] : $grup;
    }

    /**
     * @return list<string>
     */
    private function potongTeks(string $teks, int $batas = 500): array
    {
        $teks = trim($teks);
        if ($teks === '' || mb_strlen($teks) <= $batas) {
            return $teks === '' ? [] : [$teks];
        }

        $hasil = [];
        while (mb_strlen($teks) > $batas) {
            $potong = mb_substr($teks, 0, $batas);
            $spasi = mb_strrpos($potong, ' ');
            if ($spasi === false || $spasi < (int) ($batas * 0.6)) {
                $spasi = $batas;
            }
            $hasil[] = trim(mb_substr($teks, 0, $spasi));
            $teks = trim(mb_substr($teks, $spasi));
        }

        if ($teks !== '') {
            $hasil[] = $teks;
        }

        return $hasil;
    }
}

<?php

namespace App\Services\Pspk;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Penyimpanan PDF paket analisa kasus PSPK (satu berkas dibagi banyak soal Ankas lv.3–4).
 */
final class StorePspkKasusPdf
{
    public const DISK = 'local';

    /** Direktori di disk lokal (dipertahankan agar berkas dari migrasi lampiran per-soal tetap valid). */
    public const DIRECTORY = 'pspk/soal-lampiran';

    public static function store(UploadedFile $file): string
    {
        self::assertPdfMagicBytes($file);

        $filename = Str::uuid().'.pdf';

        $path = $file->storeAs(self::DIRECTORY, $filename, self::DISK);
        if ($path === false) {
            throw new InvalidArgumentException('Gagal menyimpan lampiran PDF.');
        }

        return $path;
    }

    public static function deleteIfExists(?string $path): void
    {
        if ($path === null || $path === '' || str_contains($path, '..')) {
            return;
        }

        if (! str_starts_with($path, self::DIRECTORY.'/')) {
            return;
        }

        if (Storage::disk(self::DISK)->exists($path)) {
            Storage::disk(self::DISK)->delete($path);
        }
    }

    /** Respons unduh/inline PDF yang tersimpan (hanya setelah middleware auth memverifikasi pemanggil). */
    public static function pdfResponse(string $path, string $downloadFilename): Response
    {
        if (str_contains($path, '..') || ! str_starts_with($path, self::DIRECTORY.'/')) {
            abort(404);
        }

        $disk = Storage::disk(self::DISK);
        if (! $disk->exists($path)) {
            abort(404);
        }

        return $disk->response($path, $downloadFilename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Pengiriman inline agar PDF tidak terbuka langsung di tab (hindari viewer bawaan browser + unduh).
     * Dipakai hanya setelah validasi header khusus di controller.
     */
    public static function inlineFileResponse(string $path): Response
    {
        if (str_contains($path, '..') || ! str_starts_with($path, self::DIRECTORY.'/')) {
            abort(404);
        }

        $disk = Storage::disk(self::DISK);
        if (! $disk->exists($path)) {
            abort(404);
        }

        $absolute = $disk->path($path);

        return response()->file($absolute, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private static function assertPdfMagicBytes(UploadedFile $file): void
    {
        $stream = fopen($file->getRealPath(), 'rb');
        if ($stream === false) {
            throw new InvalidArgumentException('Berkas tidak dapat dibaca.');
        }

        try {
            $header = fread($stream, 5);
            if ($header !== '%PDF-') {
                throw new InvalidArgumentException('Berkas bukan PDF yang valid.');
            }
        } finally {
            fclose($stream);
        }
    }
}

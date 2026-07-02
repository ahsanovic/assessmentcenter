<?php

namespace App\Services\Pegawai;

use App\Models\RefPegawai;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

final class PegawaiQrCodeService
{
    public const DISK = 'local';

    public const DIRECTORY = 'pegawai/qrcode';

    public static function buildPayload(string $nama, string $nip): string
    {
        return "Nama: {$nama}\nNIP: {$nip}";
    }

    public static function generate(RefPegawai $pegawai): string
    {
        $payload = self::buildPayload($pegawai->nama, $pegawai->nip);

        $options = new QROptions([
            'outputInterface' => QRGdImagePNG::class,
            'scale' => 6,
            'quietzoneSize' => 2,
        ]);

        $filename = Str::uuid().'.png';
        $relativePath = self::DIRECTORY.'/'.$filename;
        $absolutePath = Storage::disk(self::DISK)->path($relativePath);

        Storage::disk(self::DISK)->makeDirectory(self::DIRECTORY);

        (new QRCode($options))->render($payload, $absolutePath);

        self::deleteIfExists($pegawai->qrcode_path);

        $pegawai->update(['qrcode_path' => $relativePath]);

        return $relativePath;
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

    public static function absolutePath(?string $path): ?string
    {
        if ($path === null || $path === '' || str_contains($path, '..')) {
            return null;
        }

        if (! str_starts_with($path, self::DIRECTORY.'/')) {
            return null;
        }

        $disk = Storage::disk(self::DISK);

        if (! $disk->exists($path)) {
            return null;
        }

        return $disk->path($path);
    }

    public static function base64DataUri(?string $path): ?string
    {
        if ($path === null || $path === '' || str_contains($path, '..')) {
            return null;
        }

        if (! str_starts_with($path, self::DIRECTORY.'/')) {
            return null;
        }

        $disk = Storage::disk(self::DISK);

        if (! $disk->exists($path)) {
            return null;
        }

        $content = $disk->get($path);

        return 'data:image/png;base64,'.base64_encode($content);
    }

    public static function imageResponse(string $path): Response
    {
        if (str_contains($path, '..') || ! str_starts_with($path, self::DIRECTORY.'/')) {
            abort(404);
        }

        $disk = Storage::disk(self::DISK);

        if (! $disk->exists($path)) {
            abort(404);
        }

        return $disk->response($path, basename($path), [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}

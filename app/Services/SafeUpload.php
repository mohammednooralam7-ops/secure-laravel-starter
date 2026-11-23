<?php
namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use InvalidArgumentException;

class SafeUpload
{
    private const ALLOWED = [
        'application/pdf' => 'pdf',
        'image/jpeg'      => 'jpg',
        'image/png'       => 'png',
    ];
    private const MAX_BYTES = 2 * 1024 * 1024;

    /** Returns the stored relative path. Uses real MIME sniffing, a random name and a private disk. */
    public static function store(UploadedFile $file): string
    {
        if ($file->getSize() > self::MAX_BYTES) {
            throw new InvalidArgumentException('File too large');
        }
        $mime = mime_content_type($file->getRealPath());
        if (! isset(self::ALLOWED[$mime])) {
            throw new InvalidArgumentException('File type not allowed');
        }
        $name = Str::uuid() . '.' . self::ALLOWED[$mime]; // ignore client-supplied name and extension
        return $file->storeAs('private-uploads', $name, 'local');
    }
}

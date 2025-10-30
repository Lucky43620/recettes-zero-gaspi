<?php

namespace App\Support\MediaLibrary;

use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\Support\FileNamer\FileNamer;

class HashFileNamer extends FileNamer
{
    public function originalFileName(string $fileName): string
    {
        return md5($fileName . time() . uniqid());
    }

    public function conversionFileName(string $fileName, Conversion $conversion): string
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);

        return $baseName . '-' . $conversion->getName() . '.' . $extension;
    }

    public function responsiveFileName(string $fileName): string
    {
        return $fileName;
    }
}

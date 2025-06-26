<?php

namespace App\Service;

use App\Exception\ShortcodeCollisionException;
use App\Exception\ShortcodeGenerationFailedException;

class ShortCodeGenerator
{
    private const CHARSET = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
    private const CODE_LENGTH = 8;
    private const SLICE_SIZE = 6;
    private const MAX_ATTEMPTS = 5;

    public function generate(string $input): iterable
    {
        if (empty(self::CHARSET)) {
            throw new ShortcodeGenerationFailedException('CHARSET is empty.');
        }

        $hash = hash('sha256', $input, true);

        for ($attempt = 0; $attempt < self::MAX_ATTEMPTS; $attempt++) {
            $slice = substr($hash, $attempt * self::SLICE_SIZE, self::SLICE_SIZE);

            if (strlen($slice) < self::SLICE_SIZE) {
                break;
            }

            yield $this->encodeToCharset($slice);
        }

        throw new ShortcodeGenerationFailedException();
    }

    private function encodeToCharset(string $binary): string
    {
        $value = unpack('J', str_pad($binary, 8, "\0", STR_PAD_LEFT))[1];
        $base = strlen(self::CHARSET);
        $code = '';

        while ($value > 0) {
            $code = self::CHARSET[$value % $base] . $code;
            $value = intdiv($value, $base);
        }

        return str_pad($code, self::CODE_LENGTH, self::CHARSET[0], STR_PAD_LEFT);
    }
}

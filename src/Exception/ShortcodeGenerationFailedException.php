<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ShortcodeGenerationFailedException extends HttpException
{
    public function __construct(string $detail = 'Unable to generate any valid shortcode.')
    {
        parent::__construct(500, $detail);
    }
}

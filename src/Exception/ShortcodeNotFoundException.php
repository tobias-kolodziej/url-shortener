<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ShortcodeNotFoundException extends HttpException
{
    public function __construct(string $detail = 'Shortcode not found.')
    {
        parent::__construct(404, $detail);
    }
}

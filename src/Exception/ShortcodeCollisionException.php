<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ShortcodeCollisionException extends HttpException
{
    public function __construct(string $detail = 'Unable to generate unique shortcode after all attempts.')
    {
        parent::__construct(500, $detail);
    }
}

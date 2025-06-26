<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ShortcodeRedirectNotFoundException extends HttpException
{
    public function __construct(string $detail = 'No redirect target found for this shortcode.')
    {
        parent::__construct(404, $detail);
    }
}

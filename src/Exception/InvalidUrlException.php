<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidUrlException extends HttpException
{
    public function __construct(string $detail = 'The provided URL is not valid.')
    {
        parent::__construct(400, $detail);
    }
}
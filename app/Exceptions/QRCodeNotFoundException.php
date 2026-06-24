<?php

namespace App\Exceptions;

use Exception;

class QRCodeNotFoundException extends Exception
{
    public function __construct(string $message = 'QR Code tidak valid atau data tidak ditemukan.')
    {
        parent::__construct($message);
    }
}

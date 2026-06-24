<?php

namespace App\Exceptions;

use Exception;

class ShelfInUseException extends Exception
{
    public function __construct(string $message = 'Rak tidak dapat dihapus karena masih digunakan oleh buku.')
    {
        parent::__construct($message);
    }
}

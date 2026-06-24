<?php

namespace App\Exceptions;

use Exception;

class CategoryInUseException extends Exception
{
    public function __construct(string $message = 'Kategori tidak dapat dihapus karena masih digunakan oleh buku.')
    {
        parent::__construct($message);
    }
}

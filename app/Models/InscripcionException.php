<?php
declare(strict_types=1);

namespace App\Models;

use RuntimeException;

final class InscripcionException extends RuntimeException
{
    public function __construct(
        public readonly string $code,
        string $message,
        public readonly int $httpStatus,
    ) {
        parent::__construct($message);
    }
}
<?php

declare(strict_types=1);

namespace Conia\Core\Exception;

use Conia\Core\Finder\Input\Token;
use Throwable;

class ParserOutputException extends ParserException implements CoreException
{
    public function __construct(
        public readonly Token $token,
        string $message,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}

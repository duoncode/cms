<?php

declare(strict_types=1);

namespace Duon\Cms\Exception;

use Duon\Cms\Finder\Input\Token;
use Throwable;

class ParserOutputException extends ParserException implements CmsException
{
	public function __construct(
		public readonly Token $token,
		string $message,
		int $code = 0,
		?Throwable $previous = null,
	) {
		parent::__construct($message, $code, $previous);
	}
}

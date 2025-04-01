<?php

declare(strict_types=1);

namespace Duon\Cms\Value;

use Duon\Cms\Util\Html as HtmlUtil;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class Html extends Text
{
	public function __toString(): string
	{
		return $this->clean();
	}

	public function clean(
		?HtmlSanitizerConfig $config = null,
		bool $removeEmptyLines = true,
	): string {
		return HtmlUtil::sanitize($this->unwrap(), $config, $removeEmptyLines);
	}

	public function excerpt(
		int $words = 30,
		string $allowedTags = '',
	): string {
		return HtmlUtil::excerpt($this->unwrap(), $words, $allowedTags);
	}
}

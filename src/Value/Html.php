<?php

declare(strict_types=1);

namespace Conia\Value;

use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Conia\Chuck\Util\Html as Sanitizer;


class Html extends Text
{
    public function clean(
        ?HtmlSanitizerConfig $config = null,
        bool $removeEmptyLines = true,
    ): string {
        return Sanitizer::clean($this->raw(), $config, $removeEmptyLines);
    }

    public function __toString(): string
    {
        return $this->clean();
    }
}

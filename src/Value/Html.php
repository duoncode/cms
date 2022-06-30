<?php

declare(strict_types=1);

namespace Conia\Value;

use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Conia\Value;
use Chuck\Util\Html as Sanitizer;


class Html extends Value
{
    public function raw(): string
    {
        return $this->data[$this->locale];
    }

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

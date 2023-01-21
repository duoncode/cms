<?php

declare(strict_types=1);

namespace Conia\Core\Value;

use Conia\Chuck\Util\Html as Sanitizer;
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
        return Sanitizer::clean($this->raw(), $config, $removeEmptyLines);
    }
}

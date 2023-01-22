<?php

declare(strict_types=1);

namespace Conia\Core\Value;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
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
        $config = $config ?: (new HtmlSanitizerConfig())
            // Allow "safe" elements and attributes. All scripts will be removed
            // as well as other dangerous behaviors like CSS injection
            ->allowSafeElements();
        $sanitizer = new HtmlSanitizer($config);
        $result = $sanitizer->sanitize($this->raw());

        // also remove empty lines
        return $removeEmptyLines ?
            preg_replace("/(^[\r\n]*|[\r\n]+)[\\s\t]*[\r\n]+/", PHP_EOL, $result) :
            $result;
    }
}

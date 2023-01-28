<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Core\Exception\RuntimeException;

final class QueryCompiler
{
    public function __construct(public readonly array $builtins)
    {
    }

    public function compile(string $query): string
    {
        $parser = new QueryParser($query);
        $tokens = $parser->parse($query);

        return $this->build($tokens);
    }

    private function build(array $tokens): string
    {
        if (count($tokens) === 0) {
            return '';
        }

        return '';
    }
}

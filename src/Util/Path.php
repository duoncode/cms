<?php

declare(strict_types=1);

namespace Conia\Core\Util;

use Conia\Core\Exception\RuntimeException;

class Path
{
    public static function inside(string $parent, string $child): string
    {
        $parent = realpath($parent);

        if (!$parent) {
            throw new RuntimeException('Parent directory does not exist.');
        }

        $path = realpath(rtrim($parent, '\\/') . DIRECTORY_SEPARATOR . ltrim($child, '\\/'));

        if (!$path || strncmp($path, $parent, strlen($parent)) !== 0) {
            throw new RuntimeException('File does not exist or is not in the expected location.');
        }

        return $path;
    }
}

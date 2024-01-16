<?php

declare(strict_types=1);

namespace Conia\Core\Util;

use Hidehalo\Nanoid\Client;

function env(string $key, bool|string|null $default = null): mixed
{
    if (func_num_args() > 1) {
        $value = $_ENV[$key] ?? null;

        if ($value === null) {
            return $default;
        }
    } else {
        $value = $_ENV[$key];
    }

    return match ($value) {
        'true' => true,
        'false' => false,
        'null' => null,
        'empty' => '',
        default => $value,
    };
}

function nanoid()
{
    $client = new Client();

    return $client->formattedId(
        alphabet: '123456789bcdfghklmnpqrstvwxyz',
        size: 13
    );
}

function escape(string $string)
{
    return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
}

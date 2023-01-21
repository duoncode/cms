<?php

declare(strict_types=1);

namespace Conia\Core\Util;

use Hidehalo\Nanoid\Client;

if (!function_exists('Conia\Core\Util\nanoid')) {
    function nanoid()
    {
        $client = new Client();

        return $client->formattedId(
            alphabet: '123456789bcdfghklmnpqrstvwxyz',
            size: 13
        );
    }
}

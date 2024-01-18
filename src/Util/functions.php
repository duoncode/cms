<?php

declare(strict_types=1);

namespace Conia\Cms\Util;

use Hidehalo\Nanoid\Client;

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

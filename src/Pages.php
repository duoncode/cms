<?php

declare(strict_types=1);

namespace Conia;


class Pages extends Model
{
    public static function byUrl(string $url): ?array
    {
        $page = self::db()->pages->byUrl([
            'url' => $url,
        ])->one();

        if ($page) {
            $page['content'] = json_decode($page['content'], true);
        }

        return $page;
    }
}

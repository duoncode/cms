<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Quma\Database;

class Pages
{
    public function __construct(protected readonly Database $db)
    {
    }

    public function byUrl(string $url): ?array
    {
        $page = $this->db->pages->byUrl([
            'url' => $url,
        ])->one();

        if ($page) {
            $page['content'] = json_decode($page['content'], true);
        }

        return $page;
    }
}

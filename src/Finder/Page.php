<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Chuck\Request;
use Conia\Core\Config;
use Conia\Quma\Database;

class Page
{
    public function __construct(
        public readonly Database $db,
        public readonly Request $request,
        public readonly Config $config,
    ) {
    }

    public function byPath(string $path): ?array
    {
        $page = $this->db->pages->find([
            'path' => $path,
        ])->one();

        if ($page) {
            $page['content'] = json_decode($page['content'], true);
        }

        return $page;
    }

    public function find(
        string $query,
        array $types = [],
        int $limit = 0,
        string $order = '',
    ): array {
        return [];
    }
}

<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

class Page
{
    public function __construct(
        private readonly Context $context,
    ) {
    }

    public function byPath(string $path): ?array
    {
        $page = $this->context->db->pages->find([
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

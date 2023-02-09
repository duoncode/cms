<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Core\Context;
use Conia\Core\Finder;

class Page
{
    public function __construct(
        private readonly Context $context,
        private readonly Finder $find,
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
    ): array {
        return [];
    }
}

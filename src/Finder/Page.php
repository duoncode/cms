<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Chuck\Request;
use Conia\Core\Config;
use Conia\Core\Finder;
use Conia\Quma\Database;

class Page
{
    public readonly Database $db;
    public readonly Request $request;
    public readonly Config $config;

    public function __construct(
        protected readonly Finder $find,
    ) {
        $this->db = $find->db;
        $this->request = $find->request;
        $this->config = $find->config;
    }

    public function byUrl(string $url): ?array
    {
        $page = $this->db->pages->find([
            'url' => $url,
        ])->one();

        if ($page) {
            $page['content'] = json_decode($page['content'], true);
        }

        return $page;
    }
}

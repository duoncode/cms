<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Chuck\Exception\HttpBadRequest;
use Conia\Core\Context;
use Conia\Core\Finder;
use Conia\Core\Node;

class Page
{
    public function __construct(
        private readonly Context $context,
        private readonly Finder $find,
    ) {
    }

    public function byPath(string $path, ?bool $deleted = false, ?bool $published = true): ?Node
    {
        $data = $this->context->db->nodes->find([
            'path' => $path,
            'published' => $published,
            'deleted' => $deleted,
            'kind' => 'page',
        ])->one();

        if (!$data) {
            return null;
        }

        $data['content'] = json_decode($data['content'], true);
        $class = $data['classname'];

        if (is_subclass_of($class, Node::class)) {
            return new $class($this->context, $this->find, $data);
        }

        throw new HttpBadRequest();
    }

    public function find(
        string $query,
    ): array {
        return [];
    }
}

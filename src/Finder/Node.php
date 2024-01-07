<?php

declare(strict_types=1);

namespace Conia\Cms\Finder;

use Conia\Chuck\Exception\HttpBadRequest;
use Conia\Cms\Context;
use Conia\Cms\Finder\Finder;
use Conia\Cms\Node\Node as CmsNode;

class Node
{
    public function __construct(
        private readonly Context $context,
        private readonly Finder $find,
    ) {
    }

    public function byPath(
        string $path,
        ?bool $deleted = false,
        ?bool $published = true
    ): ?CmsNode {
        return $this->get([
            'path' => $path,
            'published' => $published,
            'deleted' => $deleted,
            'kind' => 'page',
        ]);
    }

    public function byUid(
        string $uid,
        ?bool $deleted = false,
        ?bool $published = true
    ): ?CmsNode {
        return $this->get([
            'uid' => $uid,
            'published' => $published,
            'deleted' => $deleted,
        ]);
    }

    public function get(
        array $params,
    ): ?CmsNode {
        $data = $this->context->db->nodes->find($params)->one();

        if (!$data) {
            return null;
        }

        $data['content'] = json_decode($data['content'], true);
        $data['editor_data'] = json_decode($data['editor_data'], true);
        $data['creator_data'] = json_decode($data['creator_data'], true);
        $data['paths'] = json_decode($data['paths'], true);
        $class = $this
            ->context
            ->registry
            ->tag(CmsNode::class)
            ->entry($data['typehandle'])
            ->definition();

        if (is_subclass_of($class, CmsNode::class)) {
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

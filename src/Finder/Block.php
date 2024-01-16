<?php

declare(strict_types=1);

namespace Conia\Cms\Finder;

use Conia\Cms\Context;
use Conia\Cms\Exception\RuntimeException;
use Conia\Cms\Finder\Finder;
use Conia\Cms\Node\Block as BlockNode;
use Conia\Cms\Node\Node;
use Conia\Cms\Renderer\Render;

class Block
{
    protected BlockNode $block;

    public function __construct(
        private readonly Context $context,
        private readonly Finder $find,
        string $uid,
        private readonly array $templateContext = [],
        ?bool $deleted = false,
        ?bool $published = true,
    ) {
        $data = $this->context->db->nodes->find([
            'uid' => $uid,
            'published' => $published,
            'deleted' => $deleted,
            'kind' => 'block',
        ])->one();
        $class = $this
            ->context
            ->registry
            ->tag(Node::class)
            ->entry($data['typehandle'])
            ->definition();

        if (!is_subclass_of($class, BlockNode::class)) {
            throw new RuntimeException('Invalid block class' . $class);
        }

        $data['content'] = json_decode($data['content'], true);
        $this->block = new $class($context, $find, $data);
    }

    public function __toString(): string
    {
        $render = new Render('template', $this->block::template());

        return $render->render($this->context->registry, array_merge([
            'block' => $this->block,
            'find' => $this->find,
            'locale' => $this->context->request->get('locale'),
        ], $this->templateContext));
    }
}

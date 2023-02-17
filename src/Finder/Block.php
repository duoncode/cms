<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Chuck\Renderer\Render;
use Conia\Core\Context;
use Conia\Core\Exception\RuntimeException;
use Conia\Core\Finder;
use Conia\Core\Type;

class Block
{
    protected Type $block;

    public function __construct(
        private readonly Context $context,
        private readonly Finder $find,
        string $uid,
        private readonly array $templateContext = [],
        ?bool $deleted,
        ?bool $published,
    ) {
        $data = $this->context->db->nodes->find([
            'uid' => $uid,
            'published' => $published,
            'deleted' => $deleted,
            'kind' => 'block',
        ])->one();

        $class = $data['classname'];

        if (!is_subclass_of($class, Type::class)) {
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

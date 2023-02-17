<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Core\Context;
use Conia\Core\Finder;
use Conia\Core\Type;
use Generator;
use Iterator;

final class Pages implements Iterator
{
    private string $whereFields = '';
    private string $whereTypes = '';
    private string $order = '';
    private ?int $limit = null;
    private ?bool $deleted = false;
    private ?bool $published = true;
    private readonly array $builtins;
    private Generator $result;

    public function __construct(
        private readonly Context $context,
        private readonly Finder $find,
    ) {
        $this->builtins = [
            'changed' => 'p.changed',
            'classname' => 'pt.classname',
            'created' => 'p.created',
            'creator' => 'uc.uid',
            'editor' => 'ue.uid',
            'deleted' => 'p.deleted',
            'id' => 'p.uid',
            'locked' => 'p.locked',
            'published' => 'p.published',
            'type' => 'pt.name',
            'uid' => 'p.uid',
        ];
    }

    public function find(string $query): self
    {
        $compiler = new QueryCompiler($this->context, $this->builtins);
        $this->whereFields = $compiler->compile($query);

        return $this;
    }

    public function types(string ...$types): self
    {
        $this->whereTypes = $this->typesCondition($types);

        return $this;
    }

    public function type(string $type): self
    {
        $this->whereTypes = $this->typesCondition([$type]);

        return $this;
    }

    public function order(string ...$order): self
    {
        $compiler = new OrderCompiler($this->builtins);
        $this->order = $compiler->compile(implode(',', $order));

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function published(?bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function deleted(?bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function rewind(): void
    {
        if (!isset($this->result)) {
            $this->fetchResult();
        }
        $this->result->rewind();
    }

    public function current(): Type
    {
        if (!isset($this->result)) {
            $this->fetchResult();
        }

        $page = $this->result->current();

        $class = $page['classname'];
        $page['content'] = json_decode($page['content'], true);
        $context = $this->context;

        return new $class($context->request, $context->config, $this, $page);
    }

    public function key(): int
    {
        return $this->result->key();
    }

    public function next(): void
    {
        $this->result->next();
    }

    public function valid(): bool
    {
        return $this->result->valid();
    }

    private function fetchResult(): void
    {
        $conditions = implode(' AND ', array_filter([
            trim($this->whereFields),
            trim($this->whereTypes),
        ], fn ($clause) => !empty($clause)));

        $this->result = $this->context->db->nodes->find([
            'condition' => $conditions,
            'deleted' => $this->deleted,
            'published' => $this->published,
            'order' => $this->order,
            'limit' => $this->limit,
            'kind' => 'page',
        ])->lazy();
    }

    private function typesCondition(array $types): string
    {
        $result = [];

        foreach ($types as $type) {
            if (class_exists($type) && is_subclass_of($type, Type::class)) {
                $result[] = 'pt.classname = ' . $this->context->db->quote($type);
            } else {
                $result[] = 'pt.name = ' . $this->context->db->quote($type);
            }
        }

        if (count($result) > 0) {
            return '    ' . implode("\n    OR", $result);
        }

        return '';
    }
}

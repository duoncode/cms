<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Core\Type;
use Generator;
use Iterator;

final class Pages implements Iterator
{
    private string $whereFields = '';
    private string $whereTypes = '';
    private string $limit = '';
    private string $order = '';
    private readonly array $builtins;
    private Generator $result;

    public function __construct(
        private readonly Context $context,
        private readonly bool $deleted,
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

    public function find(string $query, bool $deleted = false): self
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

    public function order(string ...$order): self
    {
        $compiler = new OrderCompiler($this->builtins);
        $this->order = $compiler->compile(implode(',', $order));

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit > 0 ? "\nLIMIT " . (string)$limit : '';

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

        return new $class($this->request, $this->config, $this->find, $page);
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
        $conditions = implode("    AND\n", array_filter([
            trim($this->whereFields),
            trim($this->whereTypes),
        ], fn ($clause) => !empty($clause)));
        $conditions .= $this->order . $this->limit;

        $this->result = $this->context->db->pages->find([
            'condition' => $conditions,
            'deleted' => $this->deleted,
        ])->lazy();
    }

    private function typesCondition(array $types): string
    {
        $result = [];

        foreach ($types as $type) {
            if (class_exists($type) && is_subclass_of($type, Type::class)) {
                $result[] = 'pt.classname = ' . $this->db->quote($type);
            } else {
                $result[] = 'pt.name = ' . $this->db->quote($type);
            }
        }

        if (count($result) > 0) {
            return '    ' . implode("\n    OR", $result);
        }

        return '';
    }

    private function left(string $left): string
    {
        if (str_contains('.', $left)) {
            [$l, $r] = explode('.', $left);

            return "p.content->'{$l}'->>'{$r}'";
        }

        return match ($left) {
            'editor' => 'coalesce(ue.display, coalesce(ue.username, ue.email))',
            'creator' => 'coalesce(uc.display, coalesce(uc.username, uc.email))',
            'id' => 'p.uid',
            'uid' => 'p.uid',
            default => "p.content->'{$left}'->>'value'",
        };
    }

    private function right(string $right): string
    {
        return $this->db->quote($right);
    }
}

<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Chuck\Request;
use Conia\Core\Config;
use Conia\Core\Finder;
use Conia\Core\Type;
use Conia\Quma\Database;
use Generator;
use Iterator;

class Pages implements Iterator
{
    public readonly Database $db;
    public readonly Request $request;
    public readonly Config $config;
    protected string $whereFields = '';
    protected string $whereTypes = '';
    protected string $limit = '';
    protected string $order = '';
    protected Generator $result;

    public function __construct(
        protected readonly Finder $find,
    ) {
        $this->db = $find->db;
        $this->request = $find->request;
        $this->config = $find->config;
        $this->builtins = [
            'changed' => '',
            'classname' => '',
            'created' => '',
            'creator' => '',
            'editor' => '',
            'deleted' => '',
            'id' => '',
            'locked' => '',
            'published' => '',
            'type' => '',
            'uid' => '',
            'url' => '',
        ];
    }

    public function find(string $query): self
    {
        $this->whereFields = $this->contentCondition($query);

        return $this;
    }

    public function types(string ...$types): self
    {
        $this->whereTypes = $this->typesCondition($types);

        return $this;
    }

    public function order(string ...$order): self
    {
        $this->order = $this->orderStatement(implode(',', $order));

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit > 0 ? ' LIMIT ' . (string)$limit : '';

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

    protected function fetchResult(): void
    {
        $condition = $this->whereFields . $this->whereTypes . $this->order . $this->limit;
        $this->result = $this->db->pages->find(['condition' => $condition])->lazy();
    }

    protected function contentCondition(string $query): string
    {
        $parsed = (new Compiler($this->builtins))->compile($query);
    }

    protected function typesCondition(array $types): string
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
            return ' AND (' . implode(' OR ', $result) . ')';
        }

        return '';
    }

    protected function left(string $left): string
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

    protected function right(string $right): string
    {
        return $this->db->quote($right);
    }
}

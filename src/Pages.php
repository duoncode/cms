<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Chuck\Request;
use Conia\Core\Type;
use Conia\Quma\Database;
use Iterator;

class Pages
{
    public function __construct(
        protected readonly Database $db,
        protected readonly Request $request,
        protected readonly Config $config,
    ) {
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

    public function find(
        string $query,
        array $types = [],
        int $limit = 0,
        string $order = '',
    ): Iterator {
        $contentCondition = $this->contentCondition($query);
        $typesCondition = $this->typesCondition($types);
        $limitStatement = $limit > 0 ? ' LIMIT ' . (string)$limit : '';
        $orderStatement = $this->orderStatement($order);

        return $this->runQuery(
            $contentCondition . $typesCondition . $orderStatement . $limitStatement
        );
    }

    protected function runQuery(string $condition): Iterator
    {
        $pages = $this->db->pages->find(['condition' => $condition])->lazy();

        foreach ($pages as $page) {
            $class = $page['classname'];
            $page['content'] = json_decode($page['content'], true);

            yield new $class($this->request, $this->config, $this, $page);
        }
    }

    protected function contentCondition(string $query): string
    {
        $parsed = (new Parser())->parseQuery($query);

        if (count($parsed['expressions']) === 0) {
            return '';
        }

        // $sql = '';
        $booleanOperator = $parsed['booleanOperator'];

        $expressions = [];
        foreach ($parsed['expressions'] as $expression) {
            $left = $this->left($expression['left']);
            $operator = $expression['operator'];
            $right = $this->db->quote($expression['right']);

            $expressions[] = $left . ' ' . $operator . ' ' . $right;
        }

        return match ($booleanOperator) {
            'AND' => 'AND (' . implode(' AND ', $expressions) . ')',
            'OR' => 'AND (' . implode(' OR ', $expressions) . ')',
            default => 'AND ' . $expressions[0],
        };
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

    protected function orderStatement(string $order): string
    {
        $parsed = (new Parser())->parseOrder($order);

        if (count($parsed) === 0) {
            return '';
        }

        $result = [];

        foreach ($parsed as $field) {
            $parts = explode('.', $field['field']);
            if (count($parts) > 1) {
                [$first, $second] = $parts;
            } else {
                [$first, $second] = [$parts[0], 'value'];
            }

            $result[] = match ($field['field']) {
                'created' => 'p.created ',
                'creator' => 'p.changed ',
                'id' => 'p.uid',
                'uid' => 'p.uid',
                default => "p.content->'{$first}'->>'{$second}'",
            } . ' ' . $field['direction'];
        }

        if (count($result) > 0) {
            return ' ORDER BY ' . implode(', ', $result);
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

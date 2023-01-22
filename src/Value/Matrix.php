<?php

declare(strict_types=1);

namespace Conia\Core\Value;

use Conia\Chuck\Request;
use Generator;
use ValueError;

class Matrix extends Value
{
    protected readonly Generator $localizedData;

    public function __construct(Request $request, array $data)
    {
        parent::__construct($request, $data);

        $this->localizedData = match ($data['i18n'] ?? null) {
            'separate' => $this->getSeparate($data),
            'mixed' => $this->getMixed($data),
            default => throw new ValueError('Unknown i18n setting of Matrix field'),
        };
    }

    public function __toString(): string
    {
        return $this->render();
    }

    public function render(string $tag = 'div', string $prefix = 'conia'): string
    {
        $columns = $this->data['columns'] ?? 12;

        $out = '<' . $tag . ' class="' . $prefix . '-grid ' . $prefix . '-grid-columns-' . $columns . '">';

        foreach ($this->localizedData as $value);

        $out .= '</' . $tag . '>';

        return $out;
    }

    public function renderValue(string $prefix, string $type, string $content): string
    {
        return '<div class="' . $prefix . '-' . $type;
    }

    public function json(): mixed
    {
        return [
            'columns' => $this->data['columns'] ?? null,
            'data' => $this->localizedData,
        ];
    }

    protected function getField(array $field): Value
    {
        return match ($field['type']) {
            'wysiwyg' => new Html($this->request, $field),
            'image' => new Image($this->request, $field),
            'text' => new Text($this->request, $field),
        };
    }

    protected function getMixed(array $data): Generator
    {
        foreach ($data['fields'] as $field) {
            yield $this->getField($field);
        }
    }

    protected function getSeparate(array $data): Generator
    {
        $locale = $this->locale;

        while ($locale) {
            $fields = $data[$this->locale->id] ?? null;

            if ($fields) {
                foreach ($fields as $field) {
                    yield $this->getField($field);
                }
            }

            $fallback = $this->locale->fallback();

            if ($locale === $fallback) {
                break;
            }

            $locale = $fallback;
        }
    }
}

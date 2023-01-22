<?php

declare(strict_types=1);

namespace Conia\Core\Value;

use Conia\Chuck\Request;
use Conia\Core\Type;
use Generator;
use ValueError;

class Grid extends Value
{
    protected readonly Generator $localizedData;

    public function __construct(Type $page, Request $request, array $data)
    {
        parent::__construct($page, $request, $data);

        $this->localizedData = match ($data['i18n'] ?? null) {
            'separate' => $this->getSeparate($data),
            'mixed' => $this->getMixed($data),
            default => throw new ValueError('Unknown i18n setting of Grid field'),
        };
    }

    public function __toString(): string
    {
        return $this->render();
    }

    public function json(): mixed
    {
        return [
            'columns' => $this->data['columns'] ?? null,
            'data' => $this->localizedData,
        ];
    }

    public function render(string $tag = 'div', string $prefix = 'conia'): string
    {
        $columns = $this->data['columns'] ?? 12;

        $out = '<' . $tag . ' class="' . $prefix . '-grid ' . $prefix . '-grid-columns-' . $columns . '">';

        foreach ($this->localizedData as $value) {
            $out .= $this->renderValue($prefix, $value);
        }

        $out .= '</' . $tag . '>';

        return $out;
    }

    protected function renderValue(string $prefix, GridItem $value): string
    {
        $colspan = $prefix . '-colspan-' . $value->data['colspan'];
        $rowspan = $prefix . '-rowspan-' . $value->data['rowspan'];

        $out = '<div class="' . $prefix . '-' . $value->type . " {$colspan} {$rowspan}" . '">';
        $out .= match ($value->type) {
            'html' => $value->data['value'],
            'text' => $value->data['value'],
            'image' => $this->renderImage($value->data['value']),
        };
        $out .= '</div>';

        return $out;
    }

    protected function renderImage(string $value): string
    {
        return '<img src="/assets/page/' . $this->page->uid() . '/' . $value . '">';
    }

    protected function getMixed(array $data): Generator
    {
        foreach ($data['fields'] as $field) {
            yield new GridItem($field['type'], $field);
        }
    }

    protected function getSeparate(array $data): Generator
    {
        $locale = $this->locale;

        while ($locale) {
            $fields = $data[$this->locale->id] ?? null;

            if ($fields) {
                foreach ($fields as $field) {
                    yield new GridItem($field['type'], $field);
                }

                break;
            }

            $locale = $locale->fallback();
        }
    }
}

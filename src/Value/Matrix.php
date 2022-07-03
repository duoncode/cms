<?php

declare(strict_types=1);

namespace Conia\Value;

use \Generator;
use Conia\Request;


class Matrix extends Value
{
    protected readonly Generator $localizedData;
    protected string $prefix = 'conia-';

    public function __construct(Request $request, array $data)
    {
        parent::__construct($request, $data);

        $this->localizedData = match ($data['i18n'] ?? null) {
            'separate' => $this->getSeparate($data),
            'mixed' => $this->getMixed($data),
            default => [],
        };
    }

    protected function getMixed(array $data): Generator
    {
        yield $data;
    }

    protected function getSeparate(array $data): Generator
    {
        $locale = $this->locale;

        while ($locale) {
            $fields = $data[$this->locale->id] ?? null;

            if ($fields) {
                foreach ($fields as $field) {
                    yield match ($field['type']) {
                        'wysiwyg' => new Html($this->request, $field),
                        'image' => new Images($this->request, $field),
                    };
                }
            };

            $locale = $this->locale->fallback();
        }
    }

    public function render(?string $type = null, mixed ...$args)
    {
        $context = array_merge([
            'prefix' => $this->prefix,
            'columns' => $this->data['columns'] ?? 12,
            'fields' => $this->localizedData,
        ]);

        if ($type) {
            return $this->request->renderer($type, ...$args)->render($context);
        }

        $config = $this->request->config();
        $engine = $config->templateEngine();

        return $engine->render('matrix', array_merge(
            [
                'request' => $this->request,
                'config' => $config,
            ],
            $context,
        ));
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

    public function prefix(string $prefix): static
    {
        $this->prefix = $prefix;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Conia\Value;

use Conia\Request;


class Matrix extends Value
{
    protected readonly string $type;
    protected readonly array $localizedData;
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

    protected function getMixed(array $data): array
    {
        return $data;
    }

    protected function getSeparate(array $data): array
    {
        $locale = $this->locale;

        while ($locale) {
            $value = $data[$this->locale->id] ?? null;

            if ($value) return $value;

            $locale = $this->locale->fallback();
        }

        return [];
    }

    public function render(?string $type = null, mixed ...$args)
    {
        $context = array_merge([
            'prefix' => $this->prefix,
            'columns' => $this->data['columns'] ?? 12,
            'fields' => $this->localizedData
        ]);

        error_log(print_r($this->localizedData, true));

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

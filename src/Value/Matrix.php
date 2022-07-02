<?php

declare(strict_types=1);

namespace Conia\Value;

use Conia\Request;


class Matrix extends Value
{
    protected readonly string $type;
    protected readonly array $localizedData;

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

    public function __toString(): string
    {
        return 'Matrix Field';
    }

    public function json(): mixed
    {
        return [
            'columns' => $this->data['columns'] ?? null,
            'data' => $this->localizedData,
        ];
    }
}

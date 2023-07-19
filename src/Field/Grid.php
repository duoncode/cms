<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Field\Field;
use Conia\Core\Value\Grid as GridValue;
use ValueError;

class Grid extends Field
{
    protected int $columns = 12;
    protected string $i18n = 'mixed';

    public function __toString(): string
    {
        return 'Grid Field';
    }

    public function columns(int $columns): static
    {
        if ($columns < 1 || $columns > 25) {
            throw new ValueError('The value of $columns must be >= 1 and <= 25');
        }

        $this->columns = $columns;

        return $this;
    }

    public function getColumns(): int
    {
        return $this->columns;
    }

    public function getI18N(): string
    {
        return $this->i18n;
    }

    public function value(): GridValue
    {
        return new GridValue($this->node, $this, $this->valueContext);
    }

    public function properties(): array
    {
        return array_merge(parent::properties(), [
            'columns' => $this->columns,
        ]);
    }

    public function structure(mixed $value = null): array
    {
        $value = $value ?: $this->default;

        if (is_array($value)) {
            return ['type' => 'grid', 'columns' => 12, 'value' => $value];
        }

        $result = ['type' => 'grid', 'columns' => 12, 'value' => []];

        if ($this->translate) {
            foreach ($this->node->config->locales() as $locale) {
                $result['value'][$locale->id] = [];
            }
        }

        return $result;
    }
}

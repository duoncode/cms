<?php

declare(strict_types=1);

namespace Conia\Core\Value;

use Conia\Core\Type;
use NumberFormatter;

class Decimal extends Value
{
    public readonly ?float $value;

    public function __construct(Type $page, ValueContext $context)
    {
        parent::__construct($page, $context);

        if (is_numeric($this->data['value'] ?? null)) {
            $this->value = floatval($this->data['value']);
        } else {
            $this->value = null;
        }
    }

    public function __toString(): string
    {
        if ($this->value === null) {
            return '';
        }

        return $this->value;
    }

    public function localize(?int $digits = 2, ?string $locale = null): string
    {
        if ($this->value) {
            $formatter = $this->getFormatter(NumberFormatter::DECIMAL, $digits, $locale);

            return $formatter->format($this->value);
        }

        return '';
    }

    public function currency(string $currency, ?int $digits = 2, ?string $locale = null): string
    {
        if ($this->value) {
            $formatter = $this->getFormatter(NumberFormatter::CURRENCY, $digits, $locale);

            return $formatter->formatCurrency($this->value, $currency);
        }

        return '';
    }

    public function json(): mixed
    {
        return $this->value;
    }

    protected function getFormatter(int $style, int $digits, ?string $locale = null): NumberFormatter
    {
        $formatter = new NumberFormatter($locale ?: $this->locale->id, $style);
        $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, $digits);

        return $formatter;
    }
}

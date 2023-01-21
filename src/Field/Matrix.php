<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Chuck\Request;
use Conia\Core\Field\Field;
use Conia\Core\Value\Matrix as MatrixValue;
use Conia\Core\Value\Value;
use ValueError;

class Matrix extends Field
{
    public const I18N_MIXED = 'mixed';
    public const I18N_SEPARATE = 'separate';

    protected int $columns = 12;
    protected string $i18n = 'mixed';

    public function __toString(): string
    {
        return 'Matrix Field';
    }

    public function columns(int $columns): static
    {
        if ($columns < 12 || $columns > 25) {
            throw new ValueError('The value of $columns must be >= 12 and <= 25');
        }

        $this->columns = $columns;

        return $this;
    }

    public function getColumns(): int
    {
        return $this->columns;
    }

    public function i18n(string $i18n): static
    {
        if ($i18n === self::I18N_MIXED || $i18n === self::I18N_SEPARATE) {
            $this->i18n = $i18n;

            return $this;
        }

        throw new ValueError('Wrong i18n value. Use the Matrix class constants');
    }

    public function getI18N(): string
    {
        return $this->i18n;
    }

    public function value(Request $request, array $data): Value
    {
        return new MatrixValue($request, $data);
    }
}

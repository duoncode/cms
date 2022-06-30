<?php

declare(strict_types=1);

namespace Conia\Field;

use \ValueError;
use Conia\Field;
use Conia\Locale;
use Conia\Value\{Value, Matrix as MatrixValue};


class Matrix extends Field
{
    public int $columns = 12;

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

    public function value(array $data, Locale $locale): Value
    {
        return new MatrixValue($data, $locale);
    }
}

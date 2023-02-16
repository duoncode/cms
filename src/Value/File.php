<?php

declare(strict_types=1);

namespace Conia\Core\Value;

use Conia\Core\Field\Field;
use Conia\Core\Type;

class File extends Value
{
    public function __construct(
        Type $node,
        Field $field,
        ValueContext $context,
        protected int $index = 0,
    ) {
        parent::__construct($node, $field, $context);
    }

    public function __toString(): string
    {
        return htmlspecialchars($this->file['file']);
    }

    public function title(): string
    {
        $locale = $this->locale;

        while ($locale) {
            $value = $this->file[$this->locale->id];

            if ($value) {
                return $value;
            }

            $locale = $this->locale->fallback();
        }

        return '';
    }

    public function json(): mixed
    {
        return [];
    }
}

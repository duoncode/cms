<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Field\Field;
use Conia\Core\Value\Youtube as YoutubeValue;

class Youtube extends Field
{
    public function value(): YoutubeValue
    {
        return new YoutubeValue($this->node, $this, $this->valueContext);
    }

    public function structure(mixed $value = null): array
    {
        return $this->getSimpleStructure('youtube', $value);
    }
}

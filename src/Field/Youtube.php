<?php

declare(strict_types=1);

namespace Conia\Cms\Field;

use Conia\Cms\Field\Field;
use Conia\Cms\Value\Youtube as YoutubeValue;

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

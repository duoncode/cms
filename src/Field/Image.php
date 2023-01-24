<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Field\Field;
use Conia\Core\Type;
use Conia\Core\Value\Image as SingleImage;
use Conia\Core\Value\Images;
use Conia\Core\Value\ValueContext;

class Image extends Field
{
    protected bool $single = false;

    public function value(Type $page, ValueContext $context): Images|SingleImage
    {
        if ($this->single) {
            return new Images($page, $this, $context);
        }

        return new SingleImage($page, $this, $context);
    }

    public function single(bool $single = true): static
    {
        $this->single = $single;

        return $this;
    }
}

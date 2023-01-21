<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Field\Field;
use Conia\Request;
use Conia\Value\Images;

class Image extends Field
{
    protected bool $single = false;

    public function value(Request $request, array $data): Images
    {
        return new Images($request, $data);
    }

    public function single(bool $single = true): static
    {
        $this->single = $single;

        return $this;
    }
}

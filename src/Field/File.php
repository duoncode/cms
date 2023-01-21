<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Request;
use Conia\Value\Files;

class File extends Field
{
    protected bool $single = false;

    public function value(request $request, array $data): Files
    {
        return new Files($request, $data);
    }

    public function single(bool $single = true): static
    {
        $this->single = $single;

        return $this;
    }
}

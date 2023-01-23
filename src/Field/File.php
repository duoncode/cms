<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Type;
use Conia\Core\Value\Files;

class File extends Field
{
    protected bool $single = false;

    public function value(Type $page, array $data): Files
    {
        return new Files($page, $data);
    }

    public function single(bool $single = true): static
    {
        $this->single = $single;

        return $this;
    }
}

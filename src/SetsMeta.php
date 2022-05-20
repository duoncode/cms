<?php

declare(strict_types=1);

namespace Conia;

use \ReflectionClass;
use Conia\Meta;

trait SetsMeta
{
    public readonly string $name;
    public readonly ?string $template;
    public readonly int $columns;

    protected function setMeta(ReflectionClass $reflector)
    {
        $meta = $reflector->getAttributes(Meta::class)[0] ?? null;

        if ($meta) {
            $m = $meta->newInstance();
            $this->name = $m->name ?: $this->getClassName();
            $this->template = $m->template ?: null;
            $this->columns = $m->columns ?: 12;
        } else {
            $this->name = $this->getClassName();
            $this->template = null;
            $this->columns = 12;
        }
    }
}

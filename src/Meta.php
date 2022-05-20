<?php

declare(strict_types=1);

namespace Conia;

use \ReflectionClass;

trait Meta
{
    public readonly string $name;
    public readonly ?string $description;
    public readonly ?string $template;
    public readonly int $columns;

    protected function setMeta(ReflectionClass $reflector)
    {
        $meta = $reflector->getAttributes(TypeMeta::class)[0] ?? null;

        if ($meta) {
            $m = $meta->newInstance();
            $this->name = $m->name ?: $this->getClassName();
            $this->description = $m->desc ?: null;
            $this->template = $m->template ?: null;
            $this->columns = $m->columns ?: 12;
        } else {
            $this->name = $this->getClassName();
            $this->description = null;
            $this->template = null;
            $this->columns = 12;
        }
    }
}

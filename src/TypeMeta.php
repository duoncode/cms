<?php

declare(strict_types=1);

#[Attribute]
class TypeMeta
{
    use Meta;

    public function __construct(
        public readonly ?string $name = null,
        ?string $description = null,
        public readonly ?string $template = null,
        public readonly int $columns = 12,
    ) {
        $this->description = $this->sanitize($description);
        $this->validate();
    }
}

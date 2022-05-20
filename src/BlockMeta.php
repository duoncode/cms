<?php

declare(strict_types=1);

#[Attribute]
class BlockMeta
{
    use Meta;

    public readonly ?string $label;

    public function __construct(
        ?string $label = null,
        public readonly ?string $name = null,
        ?string $description = null,
        public readonly ?string $template = null,
        public readonly int $columns = 12,
    ) {
        $this->label = $this->sanitize($label);
        $this->description = $this->sanitize($description);
        $this->validate();
    }
}

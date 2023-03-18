<?php

declare(strict_types=1);

namespace Conia\Core;

abstract class Fieldset
{
    use InitializesFields;

    final public function __construct(
        protected readonly Node $node,
        protected readonly array $data,
    ) {
        $this->initFields();
    }

    /**
     * Is called after self::initFields.
     *
     * Can be used to make adjustments the already initialized fields
     */
    public function init(): void
    {
    }

    /**
     * Should return the title of the Fieldset rendererd in the control panel.
     */
    public function title(): string
    {
        return '';
    }
}

<?php

declare(strict_types=1);

namespace Conia\Core;

abstract class Fieldset
{
    use InitializesFields;

    /**
     * Is called after self::initFields.
     *
     * Can be used to make adjustments the already initialized fields
     */
    public function init(): void
    {
    }

    /**
     * Should return the general title of the node.
     *
     * Shown in the admin interface. But can also be used in the frontend.
     */
    abstract public function title(): string;
}

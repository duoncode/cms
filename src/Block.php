<?php

declare(strict_types=1);

namespace Conia;


abstract class Block
{
    public function __construct(public readonly bool $repeatable = false)
    {

    }
}

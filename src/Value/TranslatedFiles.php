<?php

declare(strict_types=1);

namespace Conia\Core\Value;

class TranslatedFiles extends Files
{
    public function current(): TranslatedFile
    {
        return $this->get($this->pointer);
    }

    protected function get(int $index): TranslatedFile
    {
        return new TranslatedFile($this->node, $this->field, $this->context, $index);
    }
}

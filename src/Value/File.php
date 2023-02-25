<?php

declare(strict_types=1);

namespace Conia\Core\Value;

use Conia\Core\Assets;
use Conia\Core\Exception\RuntimeException;
use Conia\Core\Field\Field;
use Conia\Core\Node;

class File extends Value
{
    public function __construct(
        Node $node,
        Field $field,
        ValueContext $context,
        protected int $index = 0,
    ) {
        parent::__construct($node, $field, $context);
    }

    public function __toString(): string
    {
        return htmlspecialchars($this->file['file']);
    }

    public function title(): string
    {
        $locale = $this->locale;

        while ($locale) {
            $value = $this->file[$this->locale->id];

            if ($value) {
                return $value;
            }

            $locale = $this->locale->fallback();
        }

        return '';
    }

    public function url(bool $bust = false): string
    {
        if ($url = filter_var($this->getFile($this->index)->url($bust), FILTER_VALIDATE_URL)) {
            return $url;
        }

        throw new RuntimeException('Invalid file url');
    }

    public function path(bool $bust = false): string
    {
        return filter_var($this->getFile($this->index)->path($bust), FILTER_SANITIZE_URL);
    }

    public function unwrap(): ?array
    {
        return $this->data['files'][0] ?? null;
    }

    public function json(): mixed
    {
        return [];
    }

    public function isset(): bool
    {
        return isset($this->data['files'][0]) ? true : false;
    }

    protected function getFile(int $index): Assets\File
    {
        return $this->getAssets()->file($this->assetsPath() . $this->data['files'][$index]['file']);
    }
}

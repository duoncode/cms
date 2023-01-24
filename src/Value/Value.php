<?php

declare(strict_types=1);

namespace Conia\Core\Value;

use Conia\Core\Assets;
use Conia\Core\Exception\NoSuchProperty;
use Conia\Core\Field\Field;
use Conia\Core\Locale;
use Conia\Core\Type;

abstract class Value
{
    public readonly string $fieldType;
    protected readonly Locale $locale;
    protected readonly string $fieldName;
    protected readonly array $data;
    protected readonly bool $multilang;

    public function __construct(
        protected readonly Type $page,
        protected readonly Field $field,
        ValueContext $context,
    ) {
        $this->locale = $page->request->get('locale');
        $this->data = $context->data;
        $this->fieldName = $context->fieldName;
        $this->fieldType = $field->type;
        $this->multilang = $field->isMultiLang();
    }

    public function __get(string $name): mixed
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        throw new NoSuchProperty("The field '{$this->fieldName}' doesn't have the property '{$name}'");
    }

    abstract public function __toString(): string;

    abstract public function json(): mixed;

    protected function getAssets(): \Conia\Sizer\Assets
    {
        static $assets = null;

        if (!$assets) {
            $assets = Assets::fromConfig($this->page->config);
        }

        return $assets;
    }
}

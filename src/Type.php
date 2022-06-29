<?php

declare(strict_types=1);

namespace Conia;

use \Generator;
use \ValueError;
use Chuck\Renderer\Config as RendererConfig;
use Conia\Field;


abstract class Type
{
    use SetsInfo;

    public array $authorized = [];
    public int $columns = 12;

    protected ?RendererConfig $renderer = null;
    protected array $list = [];
    protected array $fields = [];

    public final function __construct(
        ?string $label = null,
        ?string $name = null,
        ?string $description = null
    ) {
        $this->setInfo($name, $label, $description);
        $this->init();
    }

    abstract public function init(): void;
    abstract public function title(): string;

    public final function __get(string $name): Field
    {
        return $this->fields[$name];
    }

    public final function __set(string $name, Field $field): void
    {
        $this->list[] = $name;
        $this->fields[$name] = $field;
    }

    public function form(): Generator
    {
        foreach ($this->list as $field) {
            yield $this->fields[$field];
        }
    }

    public function columns(int $columns): static
    {
        if ($columns < 12 || $columns > 25) {
            throw new ValueError('The value of $columns must be >= 12 and <= 25');
        }

        $this->columns = $columns;

        return $this;
    }

    public function authorize(string ...$permissions): static
    {
        $this->authorized = array_merge($this->authorize, $permissions);

        return $this;
    }

    public function render(string $renderer, mixed ...$args): static
    {
        $this->renderer = new RendererConfig($renderer, $args);

        return $this;
    }
}

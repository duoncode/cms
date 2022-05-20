<?php

declare(strict_types=1);

namespace Conia;

use \Generator;
use \ReflectionClass;
use Conia\Authorized;
use Conia\Field;
use Conia\TypeMeta;


abstract class Type
{
    use Meta;

    public readonly array $permissions;

    protected array $list = [];
    protected array $fields = [];

    public final function __construct()
    {
        $reflector = new ReflectionClass($this::class);
        $this->setMeta($reflector);

        $permissions = $reflector->getAttributes(Authorized::class)[0] ?? null;

        if ($permissions) {
            $p = $permissions->newInstance();
            $this->permissions = $p->get();
        } else {
            $this->permissions = [];
        }
    }

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

    abstract public function init(): void;
    abstract public function title(): string;

    public function render(): string
    {
        $template = $this->template ?: strtolower($this->getClassName()) . '.php';

        return $template;
    }

    protected function getClassName(): string
    {
        return basename(str_replace('\\', '/', $this::class));
    }
}

<?php

declare(strict_types=1);

namespace Conia;

use \Exception;
use Chuck\Config as BaseConfig;


class Config extends BaseConfig
{
    protected array $types = [];

    public function __construct(array $config)
    {
        $coniaConfig = [
            'panel.path' => 'panel',
            'panel.theme' => null,
            'sql.conia' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR . 'sql',
            'session.expires' => 60 * 60 * 24,
            'session.authcookie' => $config['app'] . '_auth',
        ];

        parent::__construct(array_replace_recursive(
            $coniaConfig,
            $config,
        ));
    }

    public function addType(Type $type): void
    {
        $name = $type->name();

        if (array_key_exists($name, $this->types)) {
            $class = $type::class;
            throw new Exception("Type '$name' already exists. Instance of '$class'");
        }

        $this->types[$name] = $type;
    }

    public function types(): array
    {
        $result = [];

        foreach ($this->types as $key => $type) {
            $result[] = [
                'value' => $key,
                'label' => $type->label,
            ];
        }

        return $result;
    }
}

<?php

declare(strict_types=1);

namespace Conia;

use \Exception;
use Chuck\Config as BaseConfig;


class Config extends BaseConfig
{
    const TYPES = 'types';

    public function __construct(array $config)
    {
        $coniaConfig = [
            'panel.path' => 'panel',
            'panel.theme' => null,
            'sql.conia' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR . 'sql',
            'migrations.conia' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR . 'migrations',
            'scripts.conia' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bin',
            'session.expires' => 60 * 60 * 24,
            'session.authcookie' => $config['app'] . '_auth',
        ];
        $coniaConfig[self::TYPES] = [];

        parent::__construct(array_replace_recursive(
            $coniaConfig,
            $config,
        ));
    }

    public function addType(Type $type): void
    {
        $name = $type->name;

        if (array_key_exists($name, $this->settings[self::TYPES])) {
            $class = $type::class;
            throw new Exception("Type '$name' already exists. Instance of '$class'");
        }

        $this->settings[self::TYPES][$name] = $type;
    }

    public function types(): array
    {
        $result = [];

        foreach ($this->settings[self::TYPES] as $key => $type) {
            $result[] = [
                'value' => $key,
                'label' => $type->label,
            ];
        }

        return $result;
    }

    public function type(string $name): Type
    {
        return $this->settings[self::TYPES][$name];
    }
}

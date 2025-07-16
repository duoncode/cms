<?php

declare(strict_types=1);

namespace Duon\Cms\Node;

use Duon\Cms\Node\Attr\Handle;
use Duon\Cms\Node\Attr\Name;
use Duon\Cms\Node\Attr\Permission;
use Duon\Cms\Node\Attr\Render;
use Duon\Cms\Node\Attr\Route;
use ReflectionClass;

class Meta
{
	public readonly string $name; // The public name of the node type
	public readonly string $handle; // Used also as slug to address the node type in the panel
	public readonly string $renderer;
	public readonly string|array $route;
	public readonly string|array $permission;

	public function __construct(private readonly Node $node)
	{
		$attributes = $this->initAttributes();
		$this->name = $this->getName($attributes[Name::class] ?? null);
		$this->handle = $this->getHandle($attributes[Handle::class] ?? null);
		$this->renderer = $this->getRenderer($attributes[Render::class] ?? null, $attributes[Handle::class] ?? null);
		$this->route = $this->getRoute($attributes[Route::class] ?? null);
		$this->permission = $this->getPermission($attributes[Permission::class] ?? null);
	}

	private function initAttributes(): array
	{
		$reflection = new ReflectionClass($this->node);
		$attributes = $reflection->getAttributes();
		$map = [];

		foreach ($attributes as $attribute) {
			$instance = $attribute->newInstance();
			$map[$instance::class] = $instance;
		}

		return $map;
	}

	private function getName(?Name $name): string
	{
		if ($name) {
			return $name->value;
		}

		return $this->getClassName();
	}

	private function getHandle(?Handle $handle): string
	{
		if ($handle) {
			return $handle->value;
		}

		return ltrim(
			strtolower(preg_replace(
				'/[A-Z]([A-Z](?![a-z]))*/',
				'-$0',
				$this->getClassName(),
			)),
			'-',
		);
	}

	private function getRenderer(?Render $render, ?Handle $handle): string
	{
		if ($render) {
			return $render->value;
		}

		return $this->getHandle($handle);
	}

	private function getRoute(?Route $route): array|string
	{
		if ($route) {
			return $route->value;
		}

		return '';
	}

	private function getPermission(?Permission $permission): array|string
	{
		if ($permission) {
			return $permission->value;
		}

		return [
			'read' => 'everyone',
			'create' => 'authenticated',
			'change' => 'authenticated',
			'deeete' => 'authenticated',
		];
	}

	private function getClassName(): string
	{
		return basename(str_replace('\\', '/', $this->node::class));
	}
}

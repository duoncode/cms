<?php

declare(strict_types=1);

namespace Duon\Cms\Finder;

use Duon\Cms\Context;
use Duon\Cms\Exception\RuntimeException;

/**
 * @psalm-property-read Nodes  $nodes
 * @psalm-property-read Node   $node
 * @psalm-property-read Blocks $blocks
 * @psalm-property-read Block  $block
 * @psalm-property-read Menu   $menu
 */
class Finder
{
	public function __construct(private readonly Context $context) {}

	public function __get($key): Nodes|Node|Block|Menu
	{
		return match ($key) {
			'nodes' => new Nodes($this->context, $this),
			'node' => new Node($this->context, $this),
			default => throw new RuntimeException('Property not supported'),
		};
	}

	public function nodes(
		string $query = '',
	): Nodes {
		return (new Nodes($this->context, $this))->filter($query);
	}

	public function node(
		string $query,
		array $types = [],
		int $limit = 0,
		string $order = '',
	): array {
		return (new Node($this->context, $this))->find($query, $types, $limit, $order);
	}

	public function menu(string $menu): Menu
	{
		return new Menu($this->context, $menu);
	}

	public function block(
		string $uid,
		array $templateContext = [],
		?bool $deleted = false,
		?bool $published = true,
	): Block {
		return new Block($this->context, $this, $uid, $templateContext, $deleted, $published);
	}
}

<?php

declare(strict_types=1);

namespace Duon\Cms;

use Closure;
use Duon\Cms\Exception\RuntimeException;

final class Section extends NavigationItem implements NavGroup
{
	/** @var list<NavigationItem> */
	private array $children = [];

	private readonly ?Closure $onCollection;

	public function __construct(
		string $label,
		?Closure $onCollection = null,
	) {
		$label = trim($label);

		if ($label === '') {
			throw new RuntimeException('Section labels must not be empty');
		}

		parent::__construct(new NavMeta($label));
		$this->onCollection = $onCollection;
	}

	public function type(): string
	{
		return 'section';
	}

	public function section(string $label): self
	{
		$section = new self($label, $this->onCollection);
		$this->children[] = $section;

		return $section;
	}

	public function collection(string $class): CollectionRef
	{
		$collection = new CollectionRef($this, $class);
		$this->children[] = $collection;

		if ($this->onCollection !== null) {
			($this->onCollection)($collection);
		}

		return $collection;
	}

	/** @return list<NavigationItem> */
	public function children(): array
	{
		return $this->children;
	}
}

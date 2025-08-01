<?php

declare(strict_types=1);

namespace Duon\Cms\View;

interface Query
{
	public bool|null $map { get; }
	public string|null $query { get; }
	public bool|null $published { get; }
	public bool|null $hidden { get; }
	public bool|null $deleted { get; }
	public bool|null $content { get; }
	public array $uids { get; }
	public string $order { get; }
	public array $fields { get; }
}

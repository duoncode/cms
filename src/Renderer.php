<?php

declare(strict_types=1);

namespace FiveOrbs\Cms;

interface Renderer
{
	/** @param non-empty-string $id */
	public function render(string $id, array $context): string;

	public function contentType(): string;
}

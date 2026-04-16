<?php

declare(strict_types=1);

namespace Duon\Cms\View\Panel;

final class Index extends Panel
{
	public function index(): array
	{
		return $this->context();
	}
}

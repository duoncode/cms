<?php

declare(strict_types=1);

namespace Duon\Cms\View\Panel;

abstract class Panel
{
	protected string $panelDir;

	public function __construct()
	{
		$this->panelDir = __DIR__ . '/../../../panel';
	}

	protected function context(): array
	{
		return [
			'cssFiles' => [
				'tokens.css',
				'app.css',
			],
			'jsFiles' => [
				'panel.js',
			],
		];
	}
}

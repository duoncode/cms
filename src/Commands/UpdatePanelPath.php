<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\Commands;

use FiveOrbs\Cms\Config;
use FiveOrbs\Quma\Commands\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class UpdatePanelPath extends Command
{
	protected string $group = 'Admin';
	protected string $name = 'update-panel-path';
	protected string $description = 'Sets the panel path prefix in the control panel SPA';
	protected string $panelPath;
	protected string $publicPath;
	protected string $indexPath;
	protected string $defaultPath = '/cms';

	public function __construct(private Config $config)
	{
		$this->panelPath = $this->config->get('path.panel');
		$this->publicPath = $this->config->get('path.public') . $this->panelPath;
		$this->indexPath = $this->publicPath . '/index.html';
	}

	public function run(): int
	{
		$files = $this->findFiles();

		foreach ($files as $file) {
			$result = $this->replace($file);

			if ($result !== 0) {
				return $result;
			}
		}

		return 0;
	}

	private function findFiles()
	{
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->publicPath));
		$files = [];

		foreach ($iterator as $file) {
			if ($file->isFile() && in_array($file->getExtension(), ['js', 'css', 'html'])) {
				$content = file_get_contents($file->getPathname());

				if (strpos($content, $this->defaultPath) !== false) {
					$files[] = $file->getPathname();
				}
			}
		}

		return $files;
	}

	private function replace(string $file): int
	{
		if (!file_exists($file)) {
			echo("File does not exist: {$file}\n");

			return 1;
		}

		$content = file_get_contents($file);
		$updatedContent = str_replace($this->defaultPath, $this->panelPath, $content);

		if ($content === $updatedContent) {
			echo("No changes were made to the panel path: {$file}\n");

			return 0;
		}

		file_put_contents($file, $updatedContent);
		echo("Panel path updated successfully: {$file}\n");

		return 0;
	}
}

<?php

declare(strict_types=1);

namespace Duon\Cms\Commands;

use Duon\Cli\Command;
use Duon\Cms\Config;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class UpdatePanelPath extends Command
{
	protected string $group = 'Admin';
	protected string $name = 'update-panel-path';
	protected string $description = 'Updates the installed admin panel path';
	protected string $prefix;
	protected string $panelPath;
	protected string $publicPath;

	protected const string defaultPath = '/cms';

	public function __construct(
		private Config $config,
	) {
		$this->prefix = $this->config->get('path.prefix');
		$this->panelPath = $this->config->get('path.panel');
		$this->publicPath = $this->config->get('path.public') . $this->panelPath;
	}

	public function run(): int
	{
		$defaultPublicPath = $this->config->get('path.public') . self::defaultPath;
		$panelPathExists = is_dir($this->publicPath);
		$defaultPathExists = is_dir($defaultPublicPath);

		if ($this->panelPath !== self::defaultPath && !$panelPathExists && $defaultPathExists) {
			$this->info(
				'Renaming panel directory from ' . $this->removeCwdFromPath($defaultPublicPath) . ' to '
					. $this->removeCwdFromPath($this->publicPath),
			);

			if (!rename($defaultPublicPath, $this->publicPath)) {
				$this->error('Failed to rename panel directory to configured path');

				return 1;
			}

			$this->success('Renamed panel directory to configured path');
			$panelPathExists = true;
		}

		if (!$panelPathExists && !$defaultPathExists) {
			$this->error(
				'Panel directory does not exist: ' . $this->removeCwdFromPath($this->publicPath) . ' and '
					. $this->removeCwdFromPath($defaultPublicPath),
			);

			return 1;
		}

		if (!$panelPathExists) {
			$this->error('Panel directory does not exist: ' . $this->removeCwdFromPath($this->publicPath));

			return 1;
		}

		return $this->updatePanelPath();
	}

	private function updatePanelPath(): int
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

				if (strpos($content, self::defaultPath) !== false) {
					$files[] = $file->getPathname();
				}
			}
		}

		return $files;
	}

	private function replace(string $file): int
	{
		if (!file_exists($file)) {
			$this->error('File does not exist: ' . $this->removeCwdFromPath($file));

			return 1;
		}

		$content = file_get_contents($file);
		$updatedContent = str_replace(self::defaultPath, $this->prefix . $this->panelPath, $content);

		if ($content === $updatedContent) {
			$this->warn('No changes were made to the panel path: ' . $this->removeCwdFromPath($file));

			return 0;
		}

		file_put_contents($file, $updatedContent);
		$this->success('Panel path updated successfully: ' . $this->removeCwdFromPath($file));

		return 0;
	}

	private function removeCwdFromPath($path)
	{
		$cwd = realpath(getcwd());
		$absolutePath = realpath($path);

		if ($absolutePath && str_starts_with($absolutePath, $cwd)) {
			return substr($absolutePath, strlen($cwd) + 1); // +1 to remove the slash
		}

		return $path;
	}
}

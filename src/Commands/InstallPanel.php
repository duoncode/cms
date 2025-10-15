<?php

declare(strict_types=1);

namespace Duon\Cms\Commands;

use Composer\InstalledVersions;
use Duon\Cms\Config;
use Duon\Quma\Commands\Command;
use PharData;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use Throwable;

class InstallPanel extends Command
{
	protected string $group = 'Admin';
	protected string $name = 'install-panel';
	protected string $description = 'Installs or upgrades the admin panel frontend app';
	protected string $panelPath;
	protected string $publicPath;
	protected string $indexPath;

	public function __construct(private Config $config)
	{
		$this->panelPath = $this->config->get('path.panel');
		$this->publicPath = $this->config->get('path.public') . $this->panelPath;
		$this->indexPath = $this->publicPath . '/index.html';
	}

	public function run(): int
	{
		$cmsVersion = InstalledVersions::getVersion('duon/cms');

		$panelArchive = $this->downloadRelease($cmsVersion);

		if ($panelArchive !== '') {
			$this->removeDirectory($this->publicPath);
			$this->extractArchive($panelArchive, $this->publicPath);
		}

		return 0;
	}

	private function removeDirectory(string $path): void
	{
		if (!is_dir($path)) {
			return;
		}

		$this->info("Removing existing panel directory at {$path}...");

		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
			RecursiveIteratorIterator::CHILD_FIRST,
		);

		foreach ($iterator as $file) {
			if ($file->isDir()) {
				if (!rmdir($file->getPathname())) {
					$this->error("Failed to remove directory: {$file->getPathname()}");

					return;
				}
			} else {
				if (!unlink($file->getPathname())) {
					$this->error("Failed to remove file: {$file->getPathname()}");

					return;
				}
			}
		}

		if (!rmdir($path)) {
			$this->error("Failed to remove root directory: {$path}");

			return;
		}

		$this->success("Removed existing panel directory");
	}

	private function extractArchive(string $archivePath, string $destination): void
	{
		$this->info("Extracting panel archive to {$destination}...");

		$tarGzPath = null;

		try {
			// Rename the archive to have a .tar.gz extension (required by PharData)
			$tarGzPath = $archivePath . '.tar.gz';

			if (!rename($archivePath, $tarGzPath)) {
				throw new RuntimeException("Failed to rename archive");
			}

			// Open the .tar.gz archive
			$phar = new PharData($tarGzPath);

			// Ensure destination directory exists
			if (!is_dir($destination) && !mkdir($destination, 0775, true)) {
				throw new RuntimeException("Failed to create destination directory: {$destination}");
			}

			// Extract all files to destination
			$phar->extractTo($destination, null, true);

			$this->success("Panel extracted successfully");
		} catch (Throwable $e) {
			$this->error("Failed to extract archive: {$e->getMessage()}");

			// Clean up on error if archive was renamed
		} finally {
			if ($tarGzPath !== null) {
				// @unlink($tarGzPath);
			}
		}
	}

	private function downloadRelease(string $version): string
	{
		if ($version !== 'dev-main') {
			return '';
		}

		$url = 'https://github.com/duoncode/cms/releases/download/nightly/panel-nightly.tar.gz';
		$tempFile = tempnam(sys_get_temp_dir(), 'cms_panel_');

		$this->info("Downloading panel from {$url}...");

		$context = stream_context_create([
			'http' => [
				'method' => 'GET',
				'header' => 'User-Agent: Duon-CMS-Installer',
				'follow_location' => true,
			],
		]);

		$content = file_get_contents($url, false, $context);

		if ($content === false) {
			$this->error('Failed to download panel archive');

			return '';
		}

		if (file_put_contents($tempFile, $content) === false) {
			$this->error('Failed to save panel archive to temp file');

			return '';
		}

		$this->success("Downloaded panel to {$tempFile}");

		return $tempFile;
	}
}

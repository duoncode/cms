<?php

declare(strict_types=1);

namespace Duon\Cms\Commands;

use Composer\InstalledVersions;
use Duon\Cms\Config;
use Duon\Quma\Commands\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class InstallPanel extends Command
{
	protected string $group = 'Admin';
	protected string $name = 'install-panel';
	protected string $description = 'Installs or upgrades the admin panel frontend app';
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
		$cmsVersion = InstalledVersions::getVersion('duon/cms');

		$this->echoln((string) $cmsVersion, 'red');

		return 0;
	}
}

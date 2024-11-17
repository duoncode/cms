<?php

declare(strict_types=1);

namespace FiveOrbs\Cms;

use FiveOrbs\Cms\Config;
use FiveOrbs\Cms\Locale;
use FiveOrbs\Cms\Locales;
use FiveOrbs\Core\Factory;
use FiveOrbs\Core\Request;
use FiveOrbs\Quma\Database;
use FiveOrbs\Registry\Registry;

final class Context
{
	public function __construct(
		public readonly Database $db,
		public readonly Request $request,
		public readonly Config $config,
		public readonly Registry $registry,
		public readonly Factory $factory,
	) {}

	public function locales(): Locales
	{
		return $this->request->get('locales');
	}

	public function locale(): Locale
	{
		return $this->request->get('locale');
	}

	public function localeId(): string
	{
		return $this->request->get('locale')->id;
	}
}

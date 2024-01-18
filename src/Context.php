<?php

declare(strict_types=1);

namespace Conia\Cms;

use Conia\Cms\Locale;
use Conia\Core\Config;
use Conia\Core\Request;
use Conia\Quma\Database;
use Conia\Registry\Registry;

final class Context
{
    public function __construct(
        public readonly Database $db,
        public readonly Request $request,
        public readonly Config $config,
        public readonly Registry $registry,
    ) {
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

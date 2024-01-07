<?php

declare(strict_types=1);

namespace Conia\Cms;

use Conia\Chuck\Registry;
use Conia\Cms\Config;
use Conia\Cms\Locale;
use Conia\Http\Request;
use Conia\Quma\Database;

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

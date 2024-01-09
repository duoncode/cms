<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Chuck\Registry;
use Conia\Core\Config;
use Conia\Core\Locale;
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

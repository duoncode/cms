<?php

declare(strict_types=1);

namespace Conia\Cms\Value;

use IntlDateFormatter;

class Time extends DateTime
{
    public const FORMAT = 'H:i:s';

    public function localize(
        int $dateFormat = IntlDateFormatter::NONE,
        int $timeFormat = IntlDateFormatter::SHORT,
    ): string {
        return parent::localize($dateFormat, $timeFormat);
    }
}

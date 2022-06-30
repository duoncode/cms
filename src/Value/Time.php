<?php

declare(strict_types=1);

namespace Conia\Value;

use \IntlDateFormatter;


class Time extends DateTime
{
    const FORMAT = 'H:i:s';

    public function localize(
        ?string $locale = null,
        int $dateFormat = IntlDateFormatter::NONE,
        int $timeFormat = IntlDateFormatter::SHORT,
    ): string {
        return parent::localize($locale, $dateFormat, $timeFormat);
    }
}

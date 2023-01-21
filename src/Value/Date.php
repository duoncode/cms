<?php

declare(strict_types=1);

namespace Conia\Value;

use IntlDateFormatter;

class Date extends DateTime
{
    public const FORMAT = 'Y-m-d';

    public function localize(
        ?string $locale = null,
        int $dateFormat = IntlDateFormatter::MEDIUM,
        int $timeFormat = IntlDateFormatter::NONE,
    ): string {
        return parent::localize($locale, $dateFormat, $timeFormat);
    }
}

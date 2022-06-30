<?php

declare(strict_types=1);

namespace Conia\Value;

use \DateTimeImmutable;
use \DateTimeZone;
use \IntlDateFormatter;
use Conia\Locale;
use Conia\Value;


class DateTime extends Value
{
    const FORMAT = 'Y-m-d H:i:s';

    public readonly ?DateTimeImmutable $datetime;
    public readonly ?DateTimeZone $timezone;

    public function __construct(
        array $data,
        Locale $locale
    ) {
        parent::__construct($data, $locale);

        if ($data['timezone'] ?? null) {
            $this->timezone = new DateTimeZone($data['timezone']);
        } else {
            $this->timezone = null;
        }

        if ($data['value'] ?? null) {
            $this->datetime = DateTimeImmutable::createFromFormat(
                static::FORMAT,
                $data['value'],
                $this->timezone,
            );
        } else {
            $this->datetime = null;
        }
    }

    public function format(string $format): string
    {
        if ($this->datetime) {
            return $this->datetime->format($format);
        }

        return '';
    }

    public function localize(
        ?string $locale = null,
        int $dateFormat = IntlDateFormatter::MEDIUM,
        int $timeFormat = IntlDateFormatter::MEDIUM,
    ): string {
        if ($this->datetime) {
            $formatter = new IntlDateFormatter(
                $locale ?: $this->locale->id,
                $dateFormat,
                $timeFormat,
                $this->timezone
            );

            return $formatter->format($this->datetime->getTimestamp());
        }

        return '';
    }

    public function __toString(): string
    {
        return $this->format(static::FORMAT);
    }

    public function json(): mixed
    {
        return $this->__toString();
    }
}

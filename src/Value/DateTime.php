<?php

declare(strict_types=1);

namespace Conia\Core\Value;

use Conia\Chuck\Request;
use DateTimeImmutable;
use DateTimeZone;
use IntlDateFormatter;

class DateTime extends Value
{
    public const FORMAT = 'Y-m-d H:i:s';

    public readonly ?DateTimeImmutable $datetime;
    public readonly ?DateTimeZone $timezone;

    public function __construct(Request $request, array $data)
    {
        parent::__construct($request, $data);

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

    public function __toString(): string
    {
        return $this->format(static::FORMAT);
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

    public function json(): mixed
    {
        return $this->__toString();
    }
}

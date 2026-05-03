<?php

declare(strict_types=1);

namespace Duon\Cms\Validation;

use Duon\Sire\Validator;
use Duon\Sire\ValidatorRegistry;
use Duon\Sire\Value;

final class Validators
{
	public static function registry(): ValidatorRegistry
	{
		return ValidatorRegistry::withDefaults()->withMany([
			'minitems' => new Validator(
				'minitems',
				'Has fewer than the minimum number of %4$s items',
				static function (Value $value, string ...$args): bool {
					if (!is_array($value->value)) {
						return false;
					}

					return count($value->value) >= (int) ($args[0] ?? 0);
				},
				false,
			),
			'maxitems' => new Validator(
				'maxitems',
				'Has more than the maximum allowed number of %4$s items',
				static function (Value $value, string ...$args): bool {
					if (!is_array($value->value)) {
						return false;
					}

					return count($value->value) <= (int) ($args[0] ?? 0);
				},
				true,
			),
		]);
	}
}

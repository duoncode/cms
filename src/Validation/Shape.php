<?php

declare(strict_types=1);

namespace Duon\Cms\Validation;

use Duon\Sire\Contract\TypeCasterRegistry as TypeCasterRegistryContract;
use Duon\Sire\Contract\ValidatorParser as ValidatorParserContract;
use Duon\Sire\Contract\ValidatorRegistry as ValidatorRegistryContract;
use Duon\Sire\Shape as SireShape;
use Duon\Sire\Validator;
use Duon\Sire\ValidatorRegistry;
use Duon\Sire\Value;

final class Shape
{
	public static function create(
		bool $list = false,
		bool $keepUnknown = false,
		?string $title = null,
		?ValidatorRegistryContract $validatorRegistry = null,
		?ValidatorParserContract $validatorParser = null,
		?TypeCasterRegistryContract $typeCasterRegistry = null,
	): SireShape {
		$shape = $list ? SireShape::list() : new SireShape();
		$shape
			->keepUnknown($keepUnknown)
			->title($title)
			->validators($validatorRegistry ?? self::validatorRegistry());

		if ($validatorParser !== null) {
			$shape->validatorParser($validatorParser);
		}

		if ($typeCasterRegistry !== null) {
			$shape->types($typeCasterRegistry);
		}

		return $shape;
	}

	public static function nullAsEmpty(mixed $value): mixed
	{
		return $value ?? [];
	}

	private static function validatorRegistry(): ValidatorRegistry
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

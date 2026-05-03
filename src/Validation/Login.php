<?php

declare(strict_types=1);

namespace Duon\Cms\Validation;

use Duon\Sire\Contract\Shape as ShapeContract;
use Duon\Sire\Result;
use Duon\Sire\Shape;
use Override;

final class Login implements ShapeContract
{
	private Shape $shape;

	public function __construct()
	{
		$this->shape = new Shape();
		$this->shape->add('login', 'text', 'required', 'maxlen:254')->label(_('Username or email'));
		$this->shape->add('password', 'text', 'required', 'maxlen:512')->label(_('Password'));
		$this->shape->add('rememberme', 'bool')->label(_('remember me'));
	}

	#[Override]
	public function validate(array $data, int $level = 1): Result
	{
		return $this->shape->validate($data, $level);
	}
}

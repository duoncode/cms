<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\Schema;

use FiveOrbs\Sire\Schema;

class Login extends Schema
{
	protected function rules(): void
	{
		$this->add('login', 'text', 'required', 'maxlen:254')->label(_('Username or email'));
		$this->add('password', 'text', 'required', 'maxlen:512')->label(_('Password'));
		$this->add('rememberme', 'bool')->label(_('remember me'));
	}
}

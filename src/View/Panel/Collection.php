<?php

declare(strict_types=1);

namespace Duon\Cms\View\Panel;

final class Collection extends Panel
{
	public function collection(string $uid): array
	{
		return array_merge(
			$this->context(),
			['uid' => $uid],
		);
	}
}

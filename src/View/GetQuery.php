<?php

declare(strict_types=1);

namespace Duon\Cms\View;

use Duon\Core\Request;

class GetQuery implements Query
{
	use HasQueryProperties;

	public function __construct(Request $request)
	{
		$this->_map = $this->tristateValue($request->param('map', 'false'));
		$this->_query = $request->param('query', null);
		$this->_published = $this->tristateValue($request->param('published', null));
		$this->_hidden = $this->tristateValue($request->param('hidden', 'false'));
		$this->_deleted = $this->tristateValue($request->param('deleted', 'false'));
		$this->_content = $this->tristateValue($request->param('content', 'false'));
		$this->_uids = array_map(fn(string $uid) => trim($uid), explode(',', $request->param('uids', '')));
		$this->_order = $request->param('order', 'changed');
		$this->_fields = explode(',', $request->param('fields', ''));
	}

	private function tristateValue(string|null $value): bool|null
	{
		if ($value === 'true') {
			return true;
		}

		if ($value === 'false') {
			return false;
		}

		return null;
	}
}

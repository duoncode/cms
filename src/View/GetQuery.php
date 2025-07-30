<?php

declare(strict_types=1);

namespace Duon\Cms\View;

use Duon\Core\Request;

class GetQuery implements Query
{
	private bool|null $_map;
	private string|null $_query;
	private bool|null $_published;
	private bool|null $_hidden;
	private bool|null $_deleted;
	private bool|null $_content;
	private array $_uids;
	private string $_order;
	private array $_fields;

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

	public bool|null $map { get => $this->_map; }
	public string|null $query { get => $this->_query; }
	public bool|null $published { get => $this->_published; }
	public bool|null $hidden { get => $this->_hidden; }
	public bool|null $deleted { get => $this->_deleted; }
	public bool|null $content { get => $this->_content; }
	public array $uids { get => $this->_uids; }
	public string $order { get => $this->_order; }
	public array $fields { get => $this->_fields; }

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

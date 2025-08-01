<?php

declare(strict_types=1);

namespace Duon\Cms\View;

trait HasQueryProperties
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
	public bool|null $map { get => $this->_map; }
	public string|null $query { get => $this->_query; }
	public bool|null $published { get => $this->_published; }
	public bool|null $hidden { get => $this->_hidden; }
	public bool|null $deleted { get => $this->_deleted; }
	public bool|null $content { get => $this->_content; }
	public array $uids { get => $this->_uids; }
	public string $order { get => $this->_order; }
	public array $fields { get => $this->_fields; }
}

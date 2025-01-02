<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\Finder\Input;

use FiveOrbs\Cms\Exception\ParserException;
use FiveOrbs\Quma\Database;

readonly class Token
{
	public function __construct(
		public TokenGroup $group,
		public TokenType $type,
		public int $position,
		public string $lexeme,
	) {}

	public static function fromList(
		TokenGroup $group,
		TokenType $type,
		int $position,
		/** @param array<Token> */
		array $list,
		Database $db,
	): self {
		return new self($group, $type, $position, self::transformList($list, $db));
	}

	public function len(): int
	{
		return strlen($this->lexeme);
	}

	/** @param $list array<Token> */
	private static function transformList(array $list, Database $db): string
	{
		$result = [];
		$type = null;

		foreach($list as $item) {
			if ($type === null) {
				$type = $item->type;
			} else {
				if ($type !== $item->type) {
					throw new ParserException('Invalid query: mixed list item types');
				}
			}

			if ($type === TokenType::String) {
				$result[] = $db->quote($item->lexeme);
			} elseif ($type === TokenType::Number) {
				$result[] = $item->lexeme;
			} else {
				throw new ParserException('Invalid query: mixed list item types');
			}
		}

		return '(' . implode(', ', $result) . ')';
	}
}

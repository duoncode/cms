<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\Finder\Output;

use FiveOrbs\Cms\Exception\ParserException;
use FiveOrbs\Cms\Finder\CompilesField;
use FiveOrbs\Cms\Finder\Input\Token;
use FiveOrbs\Cms\Finder\Input\TokenType;
use FiveOrbs\Quma\Database;

abstract readonly class Expression
{
	use CompilesField;

	protected function getOperator(TokenType $type): string
	{
		return match ($type) {
			TokenType::LeftParen => '(',
			TokenType::RightParen => ')',
			TokenType::Equal => '=',
			TokenType::Greater => '>',
			TokenType::GreaterEqual => '>=',
			TokenType::Less => '<',
			TokenType::LessEqual => '<=',
			TokenType::Like => 'LIKE',
			TokenType::ILike => 'ILIKE',
			TokenType::Unequal => '!=',
			TokenType::Unlike => 'NOT LIKE',
			TokenType::IUnlike => 'NOT ILIKE',
			TokenType::And => 'AND',
			TokenType::Or => 'OR',
			default => throw new ParserException('Invalid expression operator: ' . $type->name),
		};
	}

	protected function getOperand(Token $token, Database $db, array $builtins): string
	{
		return match ($token->type) {
			TokenType::Boolean => strtolower($token->lexeme),
			TokenType::Field => $this->compileField($token->lexeme, 'n.content'),
			TokenType::Builtin => $builtins[$token->lexeme],
			TokenType::Keyword => $this->translateKeyword($token->lexeme),
			TokenType::Null => 'NULL',
			TokenType::Number => $token->lexeme,
			TokenType::String => $db->quote($token->lexeme),
		};
	}

	protected function translateKeyword(string $keyword): string
	{
		return match ($keyword) {
			'now' => 'NOW()',
		};
	}
}

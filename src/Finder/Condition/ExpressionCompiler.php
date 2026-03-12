<?php

declare(strict_types=1);

namespace Duon\Cms\Finder\Condition;

use Duon\Cms\Context;
use Duon\Cms\Exception\ParserException;
use Duon\Cms\Exception\ParserOutputException;
use Duon\Cms\Finder\CompilesField;
use Duon\Cms\Finder\Input\Token;
use Duon\Cms\Finder\Input\TokenType;

final class ExpressionCompiler
{
	use CompilesField;

	public function __construct(
		private readonly Context $context,
		private readonly array $builtins,
	) {}

	public function compile(Part $part): string
	{
		return match (true) {
			$part instanceof Comparison => $this->compileComparison($part),
			$part instanceof Exists => $this->compileExists($part),
			$part instanceof TokenPart => $part->sql,
			default => throw new ParserException('Unsupported condition part'),
		};
	}

	private function compileComparison(Comparison $part): string
	{
		if ($part->right->type === TokenType::Null) {
			return $this->compileNullComparison($part);
		}

		if ($part->left->type === TokenType::Path || $part->right->type === TokenType::Path) {
			return $this->compilePathComparison($part);
		}

		switch ($part->operator->type) {
			case TokenType::Like:
			case TokenType::Unlike:
			case TokenType::ILike:
			case TokenType::IUnlike:
			case TokenType::In:
			case TokenType::NotIn:
				return $this->compileSqlComparison($part);
		}

		if ($part->left->type === TokenType::Field) {
			if ($part->right->type === TokenType::Builtin || $part->right->type === TokenType::Field) {
				return $this->compileSqlComparison($part);
			}

			return $this->compileJsonPathComparison($part);
		}

		if ($part->left->type === TokenType::Builtin) {
			return $this->compileSqlComparison($part);
		}

		throw new ParserOutputException(
			$part->left,
			'Only fields or `path` are allowed on the left side of an expression.',
		);
	}

	private function compileExists(Exists $part): string
	{
		if ($part->field->lexeme === '') {
			throw new ParserOutputException($part->field, 'Invalid field name in exists condition.');
		}

		return 'n.content @? '
			. $this->context->db->quote(
				'$.' . $this->fieldPath($part->field->lexeme, $part->field),
			);
	}

	private function compileNullComparison(Comparison $part): string
	{
		return match ($part->operator->type) {
			TokenType::Equal => sprintf(
				'%s IS %s',
				$this->operand($part->left),
				$this->operand($part->right),
			),
			TokenType::Unequal => sprintf(
				'%s IS NOT %s',
				$this->operand($part->left),
				$this->operand($part->right),
			),
			default => throw new ParserOutputException(
				$part->operator,
				'Only equal (=) or unequal (!=) operators are allowed in queries with an null value.',
			),
		};
	}

	private function compileJsonPathComparison(Comparison $part): string
	{
		[$operator, $jsonOperator, $right, $negate] = match ($part->operator->type) {
			TokenType::Equal => ['@@', '==', $this->jsonLiteral($part->right), false],
			TokenType::Regex => ['@?', '?', $this->regexLiteral($part->right, false), false],
			TokenType::IRegex => ['@?', '?', $this->regexLiteral($part->right, true), false],
			TokenType::NotRegex => ['@?', '?', $this->regexLiteral($part->right, false), true],
			TokenType::INotRegex => ['@?', '?', $this->regexLiteral($part->right, true), true],
			TokenType::In => ['@@', 'in', $this->jsonLiteral($part->right), false],
			TokenType::NotIn => ['@@', 'nin', $this->jsonLiteral($part->right), false],
			default => ['@@', $part->operator->lexeme, $this->jsonLiteral($part->right), false],
		};

		return sprintf(
			"%sn.content %s '$.%s %s %s'",
			$negate ? 'NOT ' : '',
			$operator,
			$this->jsonField($part->left),
			$jsonOperator,
			$right,
		);
	}

	private function compileSqlComparison(Comparison $part): string
	{
		return sprintf(
			'%s %s %s',
			$this->operand($part->left),
			$this->sqlOperator($part->operator->type),
			$this->operand($part->right),
		);
	}

	private function compilePathComparison(Comparison $part): string
	{
		[$pathToken, $valueToken, $operator] = $this->normalizePathComparison($part);
		[$localeClause, $isNegated, $condition] = $this->pathCondition($pathToken, $valueToken, $operator);

		return sprintf(
			'%sEXISTS (SELECT 1 FROM cms.urlpaths p WHERE p.node = n.node AND p.inactive IS NULL%s AND %s)',
			$isNegated ? 'NOT ' : '',
			$localeClause,
			$condition,
		);
	}

	private function operand(Token $token): string
	{
		return match ($token->type) {
			TokenType::Boolean => strtolower($token->lexeme),
			TokenType::Field => $this->compileField($token->lexeme, 'n.content'),
			TokenType::Builtin => $this->builtins[$token->lexeme],
			TokenType::Keyword => $this->keyword($token->lexeme),
			TokenType::Null => 'NULL',
			TokenType::Number => $token->lexeme,
			TokenType::String => $this->context->db->quote($token->lexeme),
			TokenType::List => $token->lexeme,
			default => throw new ParserOutputException($token, 'Unsupported operand type.'),
		};
	}

	private function sqlOperator(TokenType $type): string
	{
		return match ($type) {
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
			TokenType::In => 'IN',
			TokenType::NotIn => 'NOT IN',
			default => throw new ParserException('Invalid expression operator: ' . $type->name),
		};
	}

	private function keyword(string $keyword): string
	{
		return match ($keyword) {
			'now' => 'NOW()',
			default => throw new ParserException('Unknown keyword: ' . $keyword),
		};
	}

	private function jsonField(Token $token): string
	{
		$parts = explode('.', $token->lexeme);

		return match (count($parts)) {
			2 => $this->compileJsonField($parts),
			1 => $parts[0] . '.value',
			default => $this->compileJsonAccessor($parts, $token),
		};
	}

	private function compileJsonField(array $segments): string
	{
		return match ($segments[1]) {
			'*' => $segments[0] . '.value.*',
			'?' => $segments[0] . '.value.' . $this->context->localeId(),
			default => implode('.', $segments),
		};
	}

	private function compileJsonAccessor(array $segments, Token $token): string
	{
		$accessor = implode('.', $segments);

		if (strpos($accessor, '?') !== false) {
			throw new ParserOutputException(
				$token,
				'The questionmark is allowed after the first dot only.',
			);
		}

		return $accessor;
	}

	private function jsonLiteral(Token $token): string
	{
		return match ($token->type) {
			TokenType::String => $this->quoteJsonString($token->lexeme),
			TokenType::Number,
			TokenType::Boolean,
			TokenType::List,
			TokenType::Null => $token->lexeme,
			default => throw new ParserOutputException(
				$token,
				'The right hand side in a field expression must be a literal',
			),
		};
	}

	private function quoteJsonString(string $string): string
	{
		return sprintf(
			'"%s"',
			preg_replace(
				'/(?<!\\\\)(")/',
				'\\"',
				trim($this->context->db->quote($string), "'"),
			),
		);
	}

	private function regexLiteral(Token $token, bool $ignoreCase): string
	{
		if ($token->type !== TokenType::String) {
			throw new ParserOutputException(
				$token,
				'Only strings are allowed on the right side of a regex expressions.',
			);
		}

		$case = $ignoreCase ? ' flag "i"' : '';
		$pattern = '"' . trim($this->context->db->quote($token->lexeme), "'") . '"';

		return sprintf('(@ like_regex %s%s)', $pattern, $case);
	}

	private function fieldPath(string $field, Token $token): string
	{
		$parts = explode('.', $field);

		foreach ($parts as $part) {
			if ($part === '') {
				throw new ParserOutputException($token, 'Invalid field name in exists condition.');
			}
		}

		if (count($parts) === 1) {
			return $parts[0] . '.value';
		}

		if (count($parts) === 2 && $parts[1] === '?') {
			return $parts[0] . '.value.' . $this->context->localeId();
		}

		if (count($parts) > 2 && in_array('?', $parts, true)) {
			throw new ParserOutputException($token, 'The questionmark is allowed after the first dot only.');
		}

		if (count($parts) === 2 && $parts[1] === '*') {
			return $parts[0] . '.value.*';
		}

		return implode('.', $parts);
	}

	/** @return array{0: Token, 1: Token, 2: TokenType} */
	private function normalizePathComparison(Comparison $part): array
	{
		if ($part->left->type === TokenType::Path) {
			return [$part->left, $part->right, $part->operator->type];
		}

		if ($part->right->type !== TokenType::Path) {
			throw new ParserOutputException($part->left, 'A path expression requires a path operand.');
		}

		return [$part->right, $part->left, $this->reversePathOperator($part->operator->type)];
	}

	private function reversePathOperator(TokenType $type): TokenType
	{
		return match ($type) {
			TokenType::Greater => TokenType::Less,
			TokenType::GreaterEqual => TokenType::LessEqual,
			TokenType::Less => TokenType::Greater,
			TokenType::LessEqual => TokenType::GreaterEqual,
			default => $type,
		};
	}

	/** @return array{0: string, 1: bool, 2: string} */
	private function pathCondition(Token $pathToken, Token $valueToken, TokenType $operator): array
	{
		$localeClause = $this->pathLocaleClause($pathToken);

		return match ($operator) {
			TokenType::Equal => [$localeClause, false, $this->pathIsComparison($valueToken, true)],
			TokenType::Unequal => [$localeClause, true, $this->pathIsComparison($valueToken, true)],
			TokenType::Like => [$localeClause, false, $this->pathScalarComparison($valueToken, 'LIKE')],
			TokenType::Unlike => [$localeClause, true, $this->pathScalarComparison($valueToken, 'LIKE')],
			TokenType::ILike => [$localeClause, false, $this->pathScalarComparison($valueToken, 'ILIKE')],
			TokenType::IUnlike => [$localeClause, true, $this->pathScalarComparison($valueToken, 'ILIKE')],
			TokenType::Regex => [$localeClause, false, $this->pathScalarComparison($valueToken, '~')],
			TokenType::NotRegex => [$localeClause, true, $this->pathScalarComparison($valueToken, '~')],
			TokenType::IRegex => [$localeClause, false, $this->pathScalarComparison($valueToken, '~*')],
			TokenType::INotRegex => [$localeClause, true, $this->pathScalarComparison($valueToken, '~*')],
			TokenType::In => [$localeClause, false, $this->pathScalarComparison($valueToken, 'IN')],
			TokenType::NotIn => [$localeClause, true, $this->pathScalarComparison($valueToken, 'IN')],
			TokenType::Greater => [$localeClause, false, $this->pathScalarComparison($valueToken, '>')],
			TokenType::GreaterEqual => [$localeClause, false, $this->pathScalarComparison($valueToken, '>=')],
			TokenType::Less => [$localeClause, false, $this->pathScalarComparison($valueToken, '<')],
			TokenType::LessEqual => [$localeClause, false, $this->pathScalarComparison($valueToken, '<=')],
			default => throw new ParserOutputException($valueToken, 'Operator not supported for path expressions.'),
		};
	}

	private function pathIsComparison(Token $valueToken, bool $allowNull): string
	{
		if ($valueToken->type === TokenType::Null) {
			if (!$allowNull) {
				throw new ParserOutputException($valueToken, 'NULL is not supported for this path comparison.');
			}

			return 'p.path IS NULL';
		}

		return 'p.path = ' . $this->pathLiteral($valueToken);
	}

	private function pathScalarComparison(Token $valueToken, string $operator): string
	{
		if ($valueToken->type === TokenType::Null) {
			throw new ParserOutputException($valueToken, 'NULL is only supported with = or != for path expressions.');
		}

		return 'p.path ' . $operator . ' ' . $this->pathLiteral($valueToken);
	}

	private function pathLiteral(Token $token): string
	{
		return match ($token->type) {
			TokenType::String => $this->context->db->quote($token->lexeme),
			TokenType::Number,
			TokenType::Boolean => $this->context->db->quote($token->lexeme),
			TokenType::List => $token->lexeme,
			default => throw new ParserOutputException($token, 'Path comparisons only support literal values.'),
		};
	}

	private function pathLocaleClause(Token $pathToken): string
	{
		$parts = explode('.', $pathToken->lexeme);

		if (count($parts) === 1 || (count($parts) === 2 && $parts[1] === '*')) {
			return '';
		}

		if (count($parts) !== 2) {
			throw new ParserOutputException($pathToken, 'Invalid path selector. Use path, path.?, path.*, or path.<locale>.');
		}

		$selector = $parts[1] === '?' ? $this->context->localeId() : $parts[1];

		if (preg_match('/^[A-Za-z0-9_-]{1,64}$/', $selector) !== 1) {
			throw new ParserOutputException($pathToken, 'Invalid locale in path selector.');
		}

		return ' AND p.locale = ' . $this->context->db->quote($selector);
	}
}

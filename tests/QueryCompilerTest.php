<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\Tests;

use FiveOrbs\Cms\Context;
use FiveOrbs\Cms\Exception\ParserException;
use FiveOrbs\Cms\Exception\ParserOutputException;
use FiveOrbs\Cms\Finder\QueryCompiler;
use FiveOrbs\Cms\Tests\Setup\TestCase;

final class QueryCompilerTest extends TestCase
{
	private Context $context;

	protected function setup(): void
	{
		$this->context = new Context(
			$this->db(),
			$this->request(),
			$this->config(),
			$this->registry(),
			$this->factory(),
		);
	}

	public function testSimpleAndQuery(): void
	{
		$compiler = new QueryCompiler($this->context, ['builtin' => 'builtin']);

		$this->assertSame(
			"n.content @@ '$.field.value == 1' AND builtin = 2",
			$compiler->compile('field=1 & builtin=2'),
		);
	}

	public function testSimpleInAndNotInQuery(): void
	{
		$compiler = new QueryCompiler($this->context, ['builtin' => 'builtin']);

		$this->assertSame(
			"builtin IN ('v1', 'v2', 'v3')",
			$compiler->compile("builtin @ ['v1'  , 'v2''v3']"),
		);
		$this->assertSame(
			"builtin IN (1, 2, 3, 4)",
			$compiler->compile("builtin @ [,1, 2,3 4]"),
		);
		$this->assertSame(
			"n.content @@ '$.field.value in (1, 2, 3, 4)'",
			$compiler->compile("field @ [1,2 , 3 4]"),
		);

		$this->assertSame(
			"builtin NOT IN ('v1', 'v2', 'v3')",
			$compiler->compile("builtin !@ ['v1''v2''v3']"),
		);
		$this->assertSame(
			"builtin NOT IN (1, 2, 3, 4)",
			$compiler->compile("builtin !@ [1    2  3,,4]"),
		);
		$this->assertSame(
			"n.content @@ '$.field.value nin (1, 2, 3, 4)'",
			$compiler->compile("field !@ [1,2 , 3 4]"),
		);
	}

	public function testSimpleOrQuery(): void
	{
		$compiler = new QueryCompiler($this->context, ['builtin' => 'builtin']);

		$this->assertSame(
			"n.content @@ '$.field.value == 1' OR builtin = 2",
			$compiler->compile('field=1 | builtin=2'),
		);
	}

	public function testNestedQuery1(): void
	{
		$compiler = new QueryCompiler($this->context, ['builtin' => 'builtin']);

		$this->assertSame(
			"n.content @@ '$.field.value == 1' AND (builtin = 2 OR builtin = 3)",
			$compiler->compile('field=1 & (builtin=2|builtin=3)'),
		);
	}

	public function testNestedQuery2(): void
	{
		$compiler = new QueryCompiler($this->context, ['builtin' => 'builtin', 'another' => 't.another']);

		$this->assertSame(
			"n.content @@ '$.field.value == 1' AND (t.another = 'test' OR (builtin > 2 AND builtin < 5))",
			$compiler->compile("field=1 & (another='test'|(builtin>2 & builtin<5))"),
		);
	}

	public function testNestedQuery3(): void
	{
		$compiler = new QueryCompiler($this->context, ['builtin' => 'builtin', 'another' => 't.another']);

		$this->assertSame(
			"(builtin = 1 OR n.content @@ '$.field.value == 1')" .
				' AND ' .
				"(t.another = 'test' OR (builtin > 2 AND builtin < 5))",
			$compiler->compile("(builtin = 1 | field=1) & (another='test'|(builtin>2 & builtin<5))"),
		);
	}

	public function testNullQuery(): void
	{
		$compiler = new QueryCompiler($this->context, ['builtin' => 'builtin']);

		$this->assertSame('builtin IS NULL', $compiler->compile('builtin = null'));
	}

	public function testNotNullQuery(): void
	{
		$compiler = new QueryCompiler($this->context, ['builtin' => 'builtin']);

		$this->assertSame('builtin IS NOT NULL', $compiler->compile('builtin != null'));
	}

	public function testNullQueryWrongPosition(): void
	{
		$this->throws(ParserException::class, 'Parse error at position 1. Invalid position for a null value.');

		$compiler = new QueryCompiler($this->context, ['builtin' => 'builtin']);

		$compiler->compile('null = builtin');
	}

	public function testNullQueryWrongOperant(): void
	{
		$this->throws(ParserOutputException::class, 'Only equal (=) or unequal (!=) operators are allowed');

		$compiler = new QueryCompiler($this->context, ['builtin' => 'builtin']);

		$compiler->compile('builtin ~ null');
	}
}

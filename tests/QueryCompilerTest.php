<?php

declare(strict_types=1);

use Conia\Core\Finder\Context;
use Conia\Core\Finder\QueryCompiler;
use Conia\Core\Tests\Setup\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->context = new Context(
        $this->db(),
        $this->request(),
        $this->config()
    );
});

test('Simple AND query', function () {
    $compiler = new QueryCompiler($this->context, ['builtin' => 'builtin']);

    expect($compiler->compile('field=1 & builtin=2'))->toBe("n.content @@ '$.field.value == 1' AND builtin = 2");
});

test('Simple OR query', function () {
    $compiler = new QueryCompiler($this->context, ['builtin' => 'builtin']);

    expect($compiler->compile('field=1 | builtin=2'))->toBe("n.content @@ '$.field.value == 1' OR builtin = 2");
});

test('Nested query I', function () {
    $compiler = new QueryCompiler($this->context, ['builtin' => 'builtin']);

    expect($compiler->compile('field=1 & (builtin=2|builtin=3)'))->toBe(
        "n.content @@ '$.field.value == 1' AND (builtin = 2 OR builtin = 3)"
    );
});

test('Nested query II', function () {
    $compiler = new QueryCompiler($this->context, ['builtin' => 'builtin', 'another' => 't.another']);

    expect($compiler->compile("field=1 & (another='test'|(builtin>2 & builtin<5))"))->toBe(
        "n.content @@ '$.field.value == 1' AND (t.another = 'test' OR (builtin > 2 AND builtin < 5))"
    );
});

test('Nested query III', function () {
    $compiler = new QueryCompiler($this->context, ['builtin' => 'builtin', 'another' => 't.another']);

    expect($compiler->compile("(builtin = 1 | field=1) & (another='test'|(builtin>2 & builtin<5))"))->toBe(
        "(builtin = 1 OR n.content @@ '$.field.value == 1')" .
        ' AND ' .
        "(t.another = 'test' OR (builtin > 2 AND builtin < 5))"
    );
});

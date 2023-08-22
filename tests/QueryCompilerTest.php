<?php

declare(strict_types=1);

use Conia\Core\Context;
use Conia\Core\Exception\ParserException;
use Conia\Core\Exception\ParserOutputException;
use Conia\Core\Finder\QueryCompiler;
use Conia\Core\Tests\Setup\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->context = new Context(
        $this->db(),
        $this->request(),
        $this->config(),
        $this->registry(),
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

test('Null query', function () {
    $compiler = new QueryCompiler($this->context, ['builtin' => 'builtin']);

    expect($compiler->compile('builtin = null'))->toBe(
        'builtin IS NULL'
    );
});

test('Not Null query', function () {
    $compiler = new QueryCompiler($this->context, ['builtin' => 'builtin']);

    expect($compiler->compile('builtin != null'))->toBe(
        'builtin IS NOT NULL'
    );
});

test('Null query wrong position', function () {
    $compiler = new QueryCompiler($this->context, ['builtin' => 'builtin']);

    $compiler->compile('null = builtin');
})->throws(ParserException::class, 'Parse error at position 1. Invalid position for a null value.');

test('Null query wrong operant', function () {
    $compiler = new QueryCompiler($this->context, ['builtin' => 'builtin']);

    $compiler->compile('builtin ~ null');
})->throws(ParserOutputException::class, 'Only equal (=) or unequal (!=) operators are allowed');

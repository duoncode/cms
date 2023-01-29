<?php

declare(strict_types=1);

use Conia\Core\Exception\ParserException;
use Conia\Core\Finder\QueryParser;
use Conia\Core\Tests\Setup\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->parser = new QueryParser(['builtin']);
});

test('Parse query', function () {
    $parser = new QueryParser();
    $tokens = $parser->parse('builtin = 13 & field & (field ~ "%like" | 73 != test) & field');

    expect(count($tokens))->toBe(17);
});

test('Invalid postion for operator I', function () {
    $this->parser->parse('( =');
})->throws(ParserException::class, 'Invalid position for an operator');

test('Invalid postion for operator II', function () {
    $this->parser->parse('test = test ~');
})->throws(ParserException::class, 'Invalid position for an operator');

test('Unbalanced parenthesis I', function () {
    $this->parser->parse('((test=1)');
})->throws(ParserException::class, 'Unbalanced parenthesis');

test('Unbalanced parenthesis II', function () {
    $this->parser->parse('    )');
})->throws(ParserException::class, 'Unbalanced parenthesis');

test('Unbalanced parenthesis III', function () {
    $this->parser->parse('(');
})->throws(ParserException::class, 'Unbalanced parenthesis');

test('Invalid condition I   (position)', function () {
    $this->parser->parse('1 = 1 1 = 1');
})->throws(ParserException::class, 'Invalid position for a condition');

test('Invalid condition II  (multiple operators)', function () {
    $this->parser->parse('1 = 1 | 1 == 1');
})->throws(ParserException::class, 'Multiple operators');

test('Invalid condition III (generally invalid)', function () {
    $this->parser->parse('1 = 1 | 1 1 =');
})->throws(ParserException::class, 'Invalid condition');

test('Invalid condition IV  (builtin in exists condition)', function () {
    $this->parser->parse('1 = 1 | builtin');
})->throws(ParserException::class, 'Conditions of type `field exists`');

test('Invalid boolean operator I', function () {
    $this->parser->parse('field || 1 = 1');
})->throws(ParserException::class, 'Invalid position for a boolean operator');

test('Invalid boolean operator II', function () {
    $this->parser->parse('1 = 1 |');
})->throws(ParserException::class, 'Boolean operator at the end of the expression');

test('Invalid parenthesis I', function () {
    $this->parser->parse('1 = 1 | ()');
})->throws(ParserException::class, 'Invalid parenthesis: empty group');

test('Invalid parenthesis II', function () {
    $this->parser->parse('1 = 1 (1 = 1)');
})->throws(ParserException::class, 'Invalid position for parenthesis');

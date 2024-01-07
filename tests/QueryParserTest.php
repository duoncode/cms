<?php

declare(strict_types=1);

use Conia\Cms\Exception\ParserException;
use Conia\Cms\Finder\Output\Comparison;
use Conia\Cms\Finder\Output\Exists;
use Conia\Cms\Finder\Output\LeftParen;
use Conia\Cms\Finder\Output\Operator;
use Conia\Cms\Finder\Output\RightParen;
use Conia\Cms\Finder\Output\UrlPath;
use Conia\Cms\Finder\QueryParser;
use Conia\Cms\Tests\Setup\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->parser = new QueryParser($this->db(), ['builtin' => 'c.builtin']);
});

test('Parse query', function () {
    $parser = new QueryParser($this->db());
    $output = $parser->parse('builtin = 13 & field & (field ~ "%like" | path != test) & field');

    expect($output[0])->toBeInstanceOf(Comparison::class);
    expect($output[1])->toBeInstanceOf(Operator::class);
    expect($output[2])->toBeInstanceOf(Exists::class);
    expect($output[3])->toBeInstanceOf(Operator::class);
    expect($output[4])->toBeInstanceOf(LeftParen::class);
    expect($output[5])->toBeInstanceOf(Comparison::class);
    expect($output[6])->toBeInstanceOf(Operator::class);
    expect($output[7])->toBeInstanceOf(UrlPath::class);
    expect($output[8])->toBeInstanceOf(RightParen::class);
    expect($output[9])->toBeInstanceOf(Operator::class);
    expect($output[10])->toBeInstanceOf(Exists::class);
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

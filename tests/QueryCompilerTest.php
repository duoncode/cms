<?php

declare(strict_types=1);

use Conia\Core\Finder\QueryCompiler;
use Conia\Core\Tests\Setup\TestCase;

uses(TestCase::class);

test('Compile field', function () {
    $compiler = new QueryCompiler($this->db(), []);

    expect($compiler->compile('field = 1'))->toBe("p.content->'field'->>'value' = 1");
});

test('Compile nested field', function () {
    $compiler = new QueryCompiler($this->db(), []);

    expect($compiler->compile('field.ln.de = 1'))->toBe("p.content->'field'->'ln'->>'de' = 1");
});

test('Compile builtin', function () {
    $compiler = new QueryCompiler($this->db(), ['test' => 'table.test']);

    expect($compiler->compile('test = 1'))->toBe('table.test = 1');
});

test('Compile keyword now', function () {
    $compiler = new QueryCompiler($this->db(), ['test' => 'test']);

    expect($compiler->compile('test = now'))->toBe('test = NOW()');
});

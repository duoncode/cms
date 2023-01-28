<?php

declare(strict_types=1);

use Conia\Core\Exception\ParserException;
use Conia\Core\Finder\OrderCompiler;
use Conia\Core\Tests\Setup\TestCase;

uses(TestCase::class);

const OB = "\nORDER BY\n    ";

test('Fail on empty statement', function () {
    (new OrderCompiler([]))->compile('');
})->throws(ParserException::class, 'Empty order by clause');

test('Compile simple statement', function () {
    $oc = new OrderCompiler([]);

    expect($oc->compile('test'))->toBe(OB . "p.content->'test'->>'value' ASC");
});

test('Compile statement with builtin', function () {
    $oc = new OrderCompiler(['field' => 'p.field']);

    expect($oc->compile('field'))->toBe(OB . 'p.field ASC');
});

test('Compile statement with dotted field', function () {
    $oc = new OrderCompiler([]);

    expect($oc->compile('test.lang'))->toBe(OB . "p.content->'test'->>'lang' ASC");
    expect($oc->compile('test.lang.de'))->toBe(OB . "p.content->'test'->'lang'->>'de' ASC");
});

test('Compile mixed statement', function () {
    $oc = new OrderCompiler(['field' => 'p.field']);
    $s = OB . "p.field ASC,\n    p.content->'test'->>'value' ASC";

    expect($oc->compile('field, test'))->toBe($s);
});

test('Change direction', function () {
    $oc = new OrderCompiler([]);

    expect($oc->compile('test desc'))->toBe(OB . "p.content->'test'->>'value' DESC");
});

test('Change direction with builtin', function () {
    $oc = new OrderCompiler(['field' => 'p.field']);

    expect($oc->compile('field DeSc'))->toBe(OB . 'p.field DESC');
});

test('Compile larger mixed statement', function () {
    $oc = new OrderCompiler(['field' => 'p.field', 'column' => 'uc.column']);
    $s = ",\n    ";
    $result = OB . "p.field DESC{$s}p.content->'test'->>'value' ASC{$s}" .
        "uc.column ASC{$s}p.content->'another'->'lang'->>'en' DESC";

    expect($oc->compile('field DESC, test asc, column, another.lang.en Desc'))->toBe($result);
});

test('Fail on injection I', function () {
    $oc = new OrderCompiler();

    $oc->compile('; DROP TABLE students;');
})->throws(ParserException::class, 'Invalid query');

test('Fail on injection II', function () {
    $oc = new OrderCompiler();

    $oc->compile('--');
})->throws(ParserException::class, 'Invalid query');

test('Fail on injection III', function () {
    $oc = new OrderCompiler();

    $oc->compile('/*');
})->throws(ParserException::class, 'Invalid query');

test('Fail invalid field I', function () {
    $oc = new OrderCompiler();

    $oc->compile('field.to.');
})->throws(ParserException::class, 'Invalid query');

test('Fail invalid field II', function () {
    $oc = new OrderCompiler();

    $oc->compile('.field.to');
})->throws(ParserException::class, 'Invalid query');

test('Fail multiple commas', function () {
    $oc = new OrderCompiler();

    $oc->compile('field1,,field2');
})->throws(ParserException::class, 'Invalid query');

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

test('Json string quoting', function () {
    $compiler = new QueryCompiler($this->context, []);

    expect($compiler->compile('field = " \"\" \' "'))->toBe(
        'p.content @@ \'$.field.value == " \"\" \'\' "\''
    );

    expect($compiler->compile("field = '\"\"\"'"))->toBe(
        'p.content @@ \'$.field.value == "\"\"\""\''
    );

    expect($compiler->compile("field = 'test\\' \" \\\" '"))->toBe(
        'p.content @@ \'$.field.value == "test\'\' \" \" "\''
    );

    expect($compiler->compile('field = \'test\\\' \\"\\" "" "\\" \\""\''))->toBe(
        'p.content @@ \'$.field.value == "test\'\' \"\" \"\" \"\" \"\""\''
    );
});

test('Number operand', function () {
    $compiler = new QueryCompiler($this->context, []);

    expect($compiler->compile('field = 13'))->toBe("p.content @@ '$.field.value == 13'");
    expect($compiler->compile('field.value.de = 13'))->toBe("p.content @@ '$.field.value.de == 13'");
    expect($compiler->compile('field = 13.73'))->toBe("p.content @@ '$.field.value == 13.73'");
    expect($compiler->compile('field.value.de = 13.73'))->toBe("p.content @@ '$.field.value.de == 13.73'");
});

test('String operand', function () {
    $compiler = new QueryCompiler($this->context, []);

    expect($compiler->compile('field = "string"'))->toBe('p.content @@ \'$.field.value == "string"\'');
    expect($compiler->compile("field = 'string'"))->toBe('p.content @@ \'$.field.value == "string"\'');
    expect($compiler->compile('field = /string/'))->toBe('p.content @@ \'$.field.value == "string"\'');
    expect($compiler->compile("field.value.de = 'string'"))->toBe('p.content @@ \'$.field.value.de == "string"\'');
    expect($compiler->compile('field.value.de = "string"'))->toBe('p.content @@ \'$.field.value.de == "string"\'');
    expect($compiler->compile('field.value.de = /string/'))->toBe('p.content @@ \'$.field.value.de == "string"\'');
});

test('Boolean operand', function () {
    $compiler = new QueryCompiler($this->context, []);

    expect($compiler->compile('field = false'))->toBe("p.content @@ '$.field.value == false'");
    expect($compiler->compile('field = true'))->toBe("p.content @@ '$.field.value == true'");
    expect($compiler->compile('field.value.de = false'))->toBe("p.content @@ '$.field.value.de == false'");
    expect($compiler->compile('field.value.de = true'))->toBe("p.content @@ '$.field.value.de == true'");
});

test('Operator regex operand pattern', function () {
    $compiler = new QueryCompiler($this->context, []);

    expect($compiler->compile('field ~ /^test$/'))->toBe("p.content @? '$.field.value ? (@ like_regex \"^test$\")'");
    expect($compiler->compile('field ~* /^test$/'))->toBe("p.content @? '$.field.value ? (@ like_regex \"^test$\" flag \"i\")'");

    expect($compiler->compile('field !~ /^test$/'))->toBe("NOT p.content @? '$.field.value ? (@ like_regex \"^test$\")'");
    expect($compiler->compile('field !~* /^test$/'))->toBe("NOT p.content @? '$.field.value ? (@ like_regex \"^test$\" flag \"i\")'");
});

test('Multilang field operand', function () {
    $compiler = new QueryCompiler($this->context, []);

    expect($compiler->compile('field.* = "test"'))->toBe("p.content @@ '$.field.value.* == \"test\"'");
});

test('Builtin operand', function () {
    $compiler = new QueryCompiler($this->context, ['test' => 'table.test']);

    expect($compiler->compile('test = 1'))->toBe('table.test = 1');
});

test('Keyword now', function () {
    $compiler = new QueryCompiler($this->context, ['test' => 'test']);

    expect($compiler->compile('test = now'))->toBe('test = NOW()');
});

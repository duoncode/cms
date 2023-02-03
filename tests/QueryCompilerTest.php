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

test('Compile field with number value', function () {
    $compiler = new QueryCompiler($this->context, []);

    expect($compiler->compile('field = 13'))->toBe("p.content @@ '$.field.value == 13'");
    expect($compiler->compile('field.value.de = 13'))->toBe("p.content @@ '$.field.value.de == 13'");
    expect($compiler->compile('field = 13.73'))->toBe("p.content @@ '$.field.value == 13.73'");
    expect($compiler->compile('field.value.de = 13.73'))->toBe("p.content @@ '$.field.value.de == 13.73'");
});

test('Compile field with string value', function () {
    $compiler = new QueryCompiler($this->context, []);

    expect($compiler->compile('field = "string"'))->toBe('p.content @@ \'$.field.value == "string"\'');
    expect($compiler->compile("field = 'string'"))->toBe('p.content @@ \'$.field.value == "string"\'');
    expect($compiler->compile('field = /string/'))->toBe('p.content @@ \'$.field.value == "string"\'');
    expect($compiler->compile("field.value.de = 'string'"))->toBe('p.content @@ \'$.field.value.de == "string"\'');
    expect($compiler->compile('field.value.de = "string"'))->toBe('p.content @@ \'$.field.value.de == "string"\'');
    expect($compiler->compile('field.value.de = /string/'))->toBe('p.content @@ \'$.field.value.de == "string"\'');
});

test('Compile field with boolean value', function () {
    $compiler = new QueryCompiler($this->context, []);

    expect($compiler->compile('field = false'))->toBe("p.content @@ '$.field.value == false'");
    expect($compiler->compile('field = true'))->toBe("p.content @@ '$.field.value == true'");
    expect($compiler->compile('field.value.de = false'))->toBe("p.content @@ '$.field.value.de == false'");
    expect($compiler->compile('field.value.de = true'))->toBe("p.content @@ '$.field.value.de == true'");
});

test('Compile regex comparison', function () {
    $compiler = new QueryCompiler($this->context, []);

    expect($compiler->compile('field ~ /^test$/'))->toBe("p.content @? '$.field.value ? (@ like_regex \"^test$\")'");
    expect($compiler->compile('field ~* /^test$/'))->toBe("p.content @? '$.field.value ? (@ like_regex \"^test$\" flag \"i\")'");

    expect($compiler->compile('field !~ /^test$/'))->toBe("NOT p.content @? '$.field.value ? (@ like_regex \"^test$\")'");
    expect($compiler->compile('field !~* /^test$/'))->toBe("NOT p.content @? '$.field.value ? (@ like_regex \"^test$\" flag \"i\")'");
});

test('Compile multilang field', function () {
    $compiler = new QueryCompiler($this->context, []);

    expect($compiler->compile('field.* = "test"'))->toBe("p.content @@ '$.field.value.* == \"test\"'");
});

test('Compile builtin', function () {
    $compiler = new QueryCompiler($this->context, ['test' => 'table.test']);

    expect($compiler->compile('test = 1'))->toBe('table.test = 1');
});

test('Compile keyword now', function () {
    $compiler = new QueryCompiler($this->context, ['test' => 'test']);

    expect($compiler->compile('test = now'))->toBe('test = NOW()');
});

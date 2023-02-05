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

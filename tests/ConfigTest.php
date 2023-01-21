<?php

declare(strict_types=1);

use Conia\Config\Connection;

uses(DatabaseCase::class);

test('Add database connection', function () {
    $config = new Config('chuck');
    $conn1 = new Connection($this->getDsn(), $this->getSqlDirs());
    $config->addConnection($conn1);
    $conn2 = new Connection($this->getDsn(), $this->getSqlDirs());
    $config->addConnection($conn2, name: 'second');

    expect($config->connection())->toBe($conn1);
    expect($config->connection('second'))->toBe($conn2);
});


test('Add duplicate database connection', function () {
    $config = new Config('chuck');
    $conn1 = new Connection($this->getDsn(), $this->getSqlDirs());
    $config->addConnection($conn1);
    $conn2 = new Connection($this->getDsn(), $this->getSqlDirs());
    $config->addConnection($conn2);
})->throws(ValueError::class, 'already exists');



test('Scripts', function () {
    $config = new Config('chuck');
    $scripts = $config->scripts();

    expect(count($scripts->get()))->toBe(1);
    expect($scripts->get()[0])->toEndWith('/bin');

    $scripts->add(C::root() . C::DS . 'scripts');

    expect(count($scripts->get()))->toBe(2);
    expect($scripts->get()[0])->toEndWith(C::root() . C::DS . 'scripts');
    expect($scripts->get()[1])->toEndWith('/bin');
});

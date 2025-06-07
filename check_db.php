<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(IllwareConsoleContracts\Console\Kernel::class);

$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArrayInput(['command' => 'db:show']),
    new Symfony\Component\Console\Output\ConsoleOutput()
);

$kernel->terminate($input, $status);

exit($status);

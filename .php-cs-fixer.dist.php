<?php

$config = new SandwaveIo\PhpCsFixerConfig\Config(
    [
        '@PSR12' => true,
    ]
);
$config->getFinder()
    ->in(__DIR__ . "/src")
    ->in(__DIR__ . "/tests");

return $config;


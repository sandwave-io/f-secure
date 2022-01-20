<?php

$config = new UptimeProject\PhpCsFixerConfig\Config(
    [
        '@PSR12' => true,
    ]
);
$config->getFinder()
    ->in(__DIR__ . "/src")
    ->in(__DIR__ . "/tests");

return $config;


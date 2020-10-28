<?php

function dd() {
    ob_start();

    array_map(function ($item) {
        var_dump($item);
    }, func_get_args());

    $dump = ob_get_clean();

    die(sprintf($dump, $dump));
}

function base_path(?string $file) {
    $base = realpath(__DIR__ . '/../');

    return sprintf('%s/%s', $base, $file);
}
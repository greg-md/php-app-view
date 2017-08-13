<?php

return [
    'paths' => [
        __DIR__ . '/../resources/views',
    ],

    'compilers' => [
        [
            'extension'         => '.blade.php',
            'type'              => \Greg\AppView\ViewServiceProvider::EXTENSION_BLADE,
            'compilation_path'  => __DIR__ . '/../storage/views',
        ],
    ],
];

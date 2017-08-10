<?php

return [
    'path' => __DIR__ . '/../resources/views',

    'compilers' => [
        [
            'extension'         => '.blade.php',
            'type'              => 'blade',
            'compilation_path'  => __DIR__ . '/../storage/views',
        ],
    ],
];

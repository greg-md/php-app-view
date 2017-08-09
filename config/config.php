<?php

return [
    'path' => __DIR__ . '/../resources/views',

    'compilers' => [
        [
            'extension'       => '.blade.php',
            'type'            => 'blade',
            'compilationPath' => __DIR__ . '/../storage/views',
        ],
    ],
];

<?php

return [
    'path' => __DIR__ . '/../resources/views',

    'extensions' => [
        '.blade.php' => [
            'type' => 'blade',
            'compilerPath' => __DIR__ . '/../storage/views'
        ],
    ],
];

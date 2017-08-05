<?php

return [
    'path' => __DIR__ . '/../resources/views',

    'extensions' => [
        [
            'extension' => '.blade.php',
            'type' => 'blade',
            'compilationPath' => __DIR__ . '/../storage/views'
        ],
    ],
];

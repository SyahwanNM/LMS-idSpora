<?php

return [
    'video_only' => [
        'label' => 'Skema 10% - Video saja',
        'percent' => 10,
        'order' => 1,
        'items' => [
            'Video pembelajaran (siap tayang)',
        ],
    ],
    'module_video' => [
        'label' => 'Skema 25% - Modul + Video',
        'percent' => 25,
        'order' => 2,
        'items' => [
            'Modul pembelajaran (PDF)',
            'Video pembelajaran (siap tayang)',
        ],
    ],
    'e2e' => [
        'label' => 'Skema 35% - End-to-End (Modul + Video + Kuis)',
        'percent' => 35,
        'order' => 3,
        'items' => [
            'Modul pembelajaran (PDF)',
            'Video pembelajaran (siap tayang)',
            'Kuis dan evaluasi',
        ],
    ],
];
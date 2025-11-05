<?php

return [
    [
        // صافي الدخل حتى 600,000
        '600000' => [
            ['min' => 1,        'max' => 40000,     'rate' => 0],
            ['min' => 40001,    'max' => 55000,     'rate' => 10],
            ['min' => 55001,    'max' => 70000,     'rate' => 15],
            ['min' => 70001,    'max' => 200000,    'rate' => 20],
            ['min' => 200001,   'max' => 400000,    'rate' => 22.5],
            ['min' => 400001,   'max' => 600000,   'rate' => 25],
            ['min' => null,     'max' => null,     'rate' => 27.5],
        ],

        // صافي الدخل حتى 700,000
        '700000' => [
            ['min' => null,     'max' => null,     'rate' => 0],
            ['min' => 1,        'max' => 55000,     'rate' => 10],
            ['min' => 55001,    'max' => 70000,     'rate' => 15],
            ['min' => 70001,    'max' => 200000,    'rate' => 20],
            ['min' => 200001,   'max' => 400000,    'rate' => 22.5],
            ['min' => 400001,   'max' => 700000,   'rate' => 25],
            ['min' => null,     'max' => null,      'rate' => 27.5],
        ],

        // صافي الدخل حتى 800,000
        '800000' => [
            ['min' => null,     'max' => null,      'rate' => 0],
            ['min' => null,     'max' => null,      'rate' => 10],
            ['min' => 1,        'max' => 70000,     'rate' => 15],
            ['min' => 70001,    'max' => 200000,    'rate' => 20],
            ['min' => 200001,   'max' => 400000,    'rate' => 22.5],
            ['min' => 400001,   'max' => 800000,   'rate' => 25],
            ['min' => null,     'max' => null,      'rate' => 27.5],
        ],

        // صافي الدخل حتى 900,000
        '900000' => [
            ['min' => null,     'max' => null,      'rate' => 0],
            ['min' => null,     'max' => null,      'rate' => 10],
            ['min' => null,     'max' => null,      'rate' => 15],
            ['min' => 1,        'max' => 200000,    'rate' => 20],
            ['min' => 200001,   'max' => 400000,    'rate' => 22.5],
            ['min' => 400001,   'max' => 900000,   'rate' => 25],
            ['min' => null,     'max' => null,      'rate' => 27.5],
        ],

        // صافي الدخل حتى 1,200,000
        '1200000' => [
            ['min' => null,     'max' => null,      'rate' => 0],
            ['min' => null,     'max' => null,      'rate' => 10],
            ['min' => null,     'max' => null,      'rate' => 15],
            ['min' => null,     'max' => null,      'rate' => 20],
            ['min' => 1,        'max' => 400000,    'rate' => 22.5],
            ['min' => 400001,   'max' => 1200000,   'rate' => 25],
            ['min' => 1200001,  'max' => INF,       'rate' => 27.5],
        ],

        // صافي الدخل أكثر من 1,200,000
        'INF' => [
            ['min' => null,     'max' => null,      'rate' => 0],
            ['min' => null,     'max' => null,      'rate' => 10],
            ['min' => null,     'max' => null,      'rate' => 15],
            ['min' => null,     'max' => null,      'rate' => 20],
            ['min' => null,     'max' => null,      'rate' => 22.5],
            ['min' => 1,        'max' => 1200000,   'rate' => 25],
            ['min' => 1200001,  'max' => INF,       'rate' => 27.5],
        ],
    ],

    // الاعفاء الضرئبيه
    [
        'amount' =>    20000
    ],
];

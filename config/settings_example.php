<?php
return [
    'ISLIVE' => false,
    'LIMIT-PER-DAY' => 50,
    'LANDING-DOMAIN' => 'http://domainleads.local',
    'APPLICATION-DOMAIN' => 'http://domainleads.local',
    'ADMIN-NUM'     => 5,
    'LEVEL1-USER'   => 50,
    'LEVEL2-USER'   => 150,
    'LEVEL3-USER'   => 500,
    'RESET-PASSWORD-LIFE' => 72,
    'DB' => [
        'USER'      =>  'root',
        'PASS'      =>  'root',
        'DATABASE'  =>  'domaninleads',
        'CONNECTION'=>  'mysql',
        'HOST'      =>  '127.0.0.1',
        'PORT'      =>  3306
    ],
    'PLAN' => [
        '1' => [50, 0], // unlock restriction, super admin 
        '2' => [150,0],
        '3' => [500, 0],
        '4' => [-1, 0],
        '5' => [-1, 1],
        'L1' => 3,
        'L2' => 5
    ]
];
?>

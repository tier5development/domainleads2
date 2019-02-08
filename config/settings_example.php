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
        '1' => [50, 0, ['name' => 'dl-tier1']], // unlock restriction, super admin 
        '2' => [150,0, ['name' => 'dl-tier-special']],
        '3' => [500, 0, ['name' => 'dl-tier2']],
        '4' => [-1, 0, ['name' => 'dl-tier3']],
        '5' => [-1, 1, ['name' => 'admin']],
        'L1' => 3,
        'L2' => 5,
        'PUBLISHABLE' => [
            '1' => [50, 50],  // unlock restriction, cost
            '2' => [500, 197],
            '4' => [-1, 500]
        ]
    ]
];
?>

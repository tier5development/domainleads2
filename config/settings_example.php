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
            '1' => [50, 50, 'basic_plan.png','basic'],  // unlock restriction, cost, image, alias name
            '3' => [500, 197, 'agency_plan.png','advanced'],
            '4' => [-1, 500, 'pro_plan.png','pro']
        ],
        'NAMEMAP' => [
            'dl-tier1' => ['1', 'basic_plan.png', 'basic'],
            'dl-tier-special' => ['2','','special'],
            'dl-tier2' => ['3', 'agency_plan.png', 'advanced'],
            'dl-tier3' => ['4', 'pro_plan.png', 'pro']
        ],
        'HIGHEST-UPGRADABLE' => 4,
        'LOWEST-DOWNGRADABLE' => 1,
        'NON-DISPLAYABLE' => 2
    ]
];
?>

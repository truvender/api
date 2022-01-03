<?php

return [
    /**
     * Control if the seeder should create a user per role while seeding the data.
     */
    'create_users' => false,

    /**
     * Control if all the laratrust tables should be truncated before running the seeder.
     */
    'truncate_tables' => true,
    
    'roles_structure' => [
        'management' => [
            'users' => 'c,r,u',
            'payments' => 'c,r,u',
            'blacklist' => 'c,r,u,d',
            'config' => 'c,r,u,d',
            'contact' => 'c,r,u,d',
            'country' => 'c,r,u,d',
            'faq' => 'c,r,u,d',
            'features' => 'c,r,u',
            'post' => 'c,r,u,d',
            'post-category' => 'c,r,u,d',
        ],

        'administrator' => [
            'users' => 'c,r,u',
            'profile' => 'r,u',
            'blacklist' => 'c,r,u,d',
            'config' => 'r',
            'contact' => 'c,r,u',
            'country' => 'c,r,u',
            'faq' => 'c,r,u,d',
            'features' => 'r',
            'post' => 'c,r,u,d',
            'post-category' => 'c,r,u,d',
        ],

        'writer' => [
            'post' => 'c,r,u',
            'post-category' => 'c,r,u',
            'faq' => 'c,r,u',
        ],
        'payer' => [
            'payments' => 'c,r,u',
        ],
        'user' => [
            'profile' => 'r,u',
        ],

    ],

    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete'
    ]
];

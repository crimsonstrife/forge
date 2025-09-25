<?php

return [
    'show_company' => (bool) env('APP_SHOW_COMPANY_LINKS', false),
    'company' => [
        'title' => 'Company',
        'links' => [
            ['label' => 'About',   'href' => '/about'],
            ['label' => 'Blog',    'href' => '/blog'],
            ['label' => 'Contact', 'href' => '/contact'],
            ['label' => 'Careers', 'href' => '/careers'],
        ],
    ],
];


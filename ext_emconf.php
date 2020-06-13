<?php
// Provide detailed information and depenencies of EXT:ns_theme_freelancer
$EM_CONF['ns_theme_freelancer'] = [
    'title' => '[NITSAN] Freelancer TYPO3 Template',
    'description' => 'Every freelancer dream just came true, as we have made T3T freelancer! This beautiful TYPO3 theme is an excellent choice for any freelance agency and freelancer, as it enables them to showcase their work in a gorgeous manner. Live-Demo: https://demo.t3terminal.com/?theme=t3t-freelancer PRO Support: https://t3terminal.com/t3-freelancer-free-typo3-portfolio-template',
    'category' => 'templates',
    'author' => 'T3:Milan Rathod, FE:Mehul Nimavat, QA:Vandna Makwana',
    'author_email' => 'info@nitsan.in',
    'author_company' => 'NITSAN Technologies Pvt Ltd',
    'state' => 'stable',
    'version' => '2.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.0.0-10.9.99',
            'ns_basetheme' => '1.0.0-10.9.99',
            'gridelements' => '9.0.0-10.9.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ]
];

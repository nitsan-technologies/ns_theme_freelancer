<?php

// Provide detailed information and depenencies of EXT:ns_theme_freelancer
$EM_CONF['ns_theme_freelancer'] = [
	'title' => 'T3 Freelancer – TYPO3 Portfolio Template',
	'description' => 'A modern and responsive TYPO3 template for freelancers to showcase their work, services, and personal brand. Ideal for portfolios and professional presentations.',
	'category' => 'templates',
	'author' => 'Team T3Planet',
	'author_email' => 'info@t3planet.de',
	'author_company' => 'T3Planet',
	'state' => 'stable',
	'version' => '13.0.2',
	'constraints' => [
		'depends' => [
			'typo3' => '12.0.0-13.4.99',
			'ns_basetheme' => '12.0.0-13.4.99',
		],
		'conflicts' => [
		],
		'suggests' => [
		],
	],
];

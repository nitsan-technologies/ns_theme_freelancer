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
	'version' => '14.0.0',
	'constraints' => [
		'depends' => [
			'typo3' => '14.0.0-14.9.99',
			'ns_basetheme' => '14.0.0-14.9.99',
      
		],
		'conflicts' => [
		],
		'suggests' => [
			'visual_editor' => '1.0.0-1.99.99',
		],
	],
];

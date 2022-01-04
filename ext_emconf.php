<?php
// Provide detailed information and depenencies of EXT:ns_theme_freelancer
$EM_CONF['ns_theme_freelancer'] = [
	'title' => '[NITSAN] T3 Freelancer | Free TYPO3 Portfolio Template',
	'description' => 'Every freelancer dream just came true, as we have made T3 freelancer, Free TYPO3 Portfolio Template! This beautiful TYPO3 theme is an excellent choice for any freelance agency and freelancer, as it enables them to showcase their work in a gorgeous manner.',
	'category' => 'templates',
	'author' => 'NITSAN Technologies Pvt Ltd',
	'author_email' => 'info@nitsan.in',
	'author_company' => 'NITSAN Technologies Pvt Ltd',
	'state' => 'stable',
	'version' => '3.0.0',
	'constraints' => [
		'depends' => [
			'typo3' => '10.0.0-11.5.99',
			'ns_basetheme' => '10.0.0-11.5.99',
		],
		'conflicts' => [
		],
		'suggests' => [
		],
	],
];

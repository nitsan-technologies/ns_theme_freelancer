<?php

// Provide detailed information and depenencies of EXT:ns_theme_freelancer
$EM_CONF['ns_theme_freelancer'] = [
	'title' => 'T3 Freelancer TYPO3 Template',
	'description' => 'Are you a TYPO3 freelancer? Are you looking for a free TYPO3 template for your portfolio? Create an eye-catching portfolio and showcase your skills with the T3 Freelancer Template. This template is perfect for illustrating your portfolio and allows you to create stylish presentations.',
	'category' => 'templates',
	'author' => 'Team NITSAN',
	'author_email' => 'info@nitsan.in',
	'author_company' => 'NITSAN Technologies Pvt Ltd',
	'state' => 'stable',
	'version' => '13.0.1',
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

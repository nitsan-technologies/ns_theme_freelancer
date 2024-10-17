<?php

// Provide detailed information and depenencies of EXT:ns_theme_freelancer
$EM_CONF['ns_theme_freelancer'] = [
	'title' => '[NITSAN] T3 Freelancer TYPO3 Template',
	'description' => 'Every freelancer dream just came true, as we have ve made T3 freelancer, Free TYPO3 Portfolio Template! Live-Demo: https://demo.t3terminal.com/?theme=t3t-freelancer PRO-version: https://t3terminal.com/t3-freelancer-free-typo3-portfolio-template',
	'category' => 'templates',
	'author' => 'Team NITSAN',
	'author_email' => 'info@nitsan.in',
	'author_company' => 'NITSAN Technologies Pvt Ltd',
	'state' => 'stable',
	'version' => '13.0.0',
	'constraints' => [
		'depends' => [
			'typo3' => '13.0.0-13.4.99',
			'ns_basetheme' => '12.0.0-13.4.99',
		],
		'conflicts' => [
		],
		'suggests' => [
		],
	],
];

<?php

// Provide detailed information and depenencies of EXT:ns_theme_freelancer
$EM_CONF[$_EXTKEY] = array(
	'title' => '[NITSAN] Freelancer TYPO3 Template',
	'description' => 'Every freelancer dream just came true, as we have ve made T3 Freelancer Free TYPO3 Portfolio Template! LIVE Demo: https://demo.t3terminal.com/?theme=t3t-freelancer You can download PRO version with more-features & free-support at https://t3terminal.com/t3-freelancer-free-typo3-portfolio-template',
	'category' => 'templates',
	'author' => 'T3:Sonal Chauhan, QA:Vandna Kalivada',
	'author_email' => 'info@nitsan.in',
	'author_company' => 'NITSAN Technologies Pvt Ltd',
	'state' => 'stable',
	'version' => '1.1.1',
	'constraints' => array(
		'depends' => array(
			'typo3' => '8.0.0-10.9.99',
            'ns_basetheme' => '1.0.0-10.9.99',
			'gridelements' => '1.0.0-10.9.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	//'autoload' => array(
	//	'classmap' => array('Classes/'),
	//),
);
?>

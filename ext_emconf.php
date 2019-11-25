<?php
// Provide detailed information and depenencies of EXT:ns_theme_freelancer
$EM_CONF[$_EXTKEY] = array(
	'title' => '[NITSAN] Freelancer TYPO3 Theme',
	'description' => 'Every freelancer dream just came true, as we have made T3T freelancer! This beautiful TYPO3 theme is an excellent choice for any freelance agency and freelancer, as it enables them to showcase their work in a gorgeous manner. Demo: https://demo.t3terminal.com/t3t-freelancer/ PRO version: https://t3terminal.com/t3-freelancer-free/',
	'category' => 'templates',
	'author' => 'T3:Milan Rathod, FE:Mehul Nimavat, QA:Vandna Makwana',
	'author_email' => 'info@nitsan.in',
	'author_company' => 'NITSAN Technologies Pvt Ltd',
	'state' => 'stable',
	'version' => '1.0.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '8.0.0-9.9.99',
            'ns_basetheme' => '1.0.0-9.9.99',
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
<?php

// Provide detailed information and depenencies of EXT:ns_theme_freelancer
$EM_CONF[$_EXTKEY] = array(
	'title' => '[NITSAN] Freelancer TYPO3 Template',
	'description' => 'The child theme of EXT:ns_basetheme',
	'category' => 'templates',
	'author' => 'T3:Sonal Chauhan, QA:Vandna Kalivada',
	'author_email' => 'info@nitsan.in',
	'author_company' => 'NITSAN Technologies Pvt Ltd',
	'state' => 'stable',
	'version' => '1.1.0',
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

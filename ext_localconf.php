<?php
$_EXTKEY = "ns_theme_newage";

// Let's add default PageTSConfig for Backend layout, TCE form, Components etc.,
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig("@import 'EXT:ns_theme_freelancer/Configuration/PageTSconfig/setup.typoscript'");

// Let's add default PageTS for "Form"
$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['default'] = 'EXT:ns_theme_freelancer/Configuration/RTE/Default.yaml';
<?php
// TYPO3 Security Check
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

// Let's configuration of this extension from "Extension Manager"
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ns_theme_freelancer'] = unserialize($_EXTCONF);

// Let's add default PageTSConfig for Backend layout, TCE form, Components etc.,
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:ns_theme_freelancer/Configuration/PageTSconfig/setup.ts">');
        
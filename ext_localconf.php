<?php
// Let's configuration of this extension from "Extension Manager"
//$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY] = unserialize($_EXTCONF);

// Let's include PageTSconfig

// Let's add default PageTSConfig for Backend layout, TCE form, Components etc.,
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:ns_theme_freelancer/Configuration/PageTSconfig/setup.ts">');
        
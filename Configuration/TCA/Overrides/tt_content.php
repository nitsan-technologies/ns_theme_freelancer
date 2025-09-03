<?php

defined('TYPO3') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItemGroup(
    'tt_content', 
    'CType', 
    't3karma_content_blocks', 
    'LLL:EXT:ns_theme_t3karma/Resources/Private/Language/locallang.xlf:contentBlocks.nsfreelancer_Blocks', 
    'before:default',
);
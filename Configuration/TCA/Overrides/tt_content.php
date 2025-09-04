<?php

defined('TYPO3') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItemGroup(
    'tt_content', 
    'CType', 
    'nsfreelancer_content_blocks', 
    'LLL:EXT:ns_theme_freelancer/Resources/Private/Language/locallang.xlf:nsfreelancer_content_blocks', 
    'before:default',
);
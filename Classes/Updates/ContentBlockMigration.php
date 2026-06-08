<?php

namespace NITSAN\NsThemeFreelancer\Updates;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;
use NITSAN\NsThemeFreelancer\Service\ContentBlockMigration as MigrationService;


#[UpgradeWizard('nsThemeFreelancerContentBlockMigration')]
final class ContentBlockMigration implements UpgradeWizardInterface
{
    private $elements = [];

    public function __construct()
    {
        $this->elements =  [
            'ns_banner',
            'ns_aboutus',
            'ns_portfolio',
            'ns_social',
        ];
    }

    /**
     * Return the speaking name of this wizard
     */
    public function getTitle(): string
    {
        return 'T3NsFreelancer: Content Block Migration';
    }

    /**
     * Return the description for this wizard
     */
    public function getDescription(): string
    {
        return 'Migrate flexform content elements to content blocks.';
    }

    /**
     * Execute the update
     *
     * Called when a wizard reports that an update is necessary
     *
     * The boolean indicates whether the update was successful
     */
    public function executeUpdate(): bool
    {
        try {
            $migrationService = GeneralUtility::makeInstance(MigrationService::class);
            $migrationService->migrate($this->elements);
            return true;
        } catch (\Exception $e) {
            \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($e,__FILE__.''.__LINE__);die;
            return false;
        }
    }

    /**
     * Is an update necessary?
     *
     * Is used to determine whether a wizard needs to be run.
     * Check if data for migration exists.
     *
     * @return bool Whether an update is required (TRUE) or not (FALSE)
     */
    public function updateNecessary(): bool
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tt_content');

        $count = $queryBuilder
            ->count('uid')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->in(
                    'CType',
                    $queryBuilder->createNamedParameter($this->elements,Connection::PARAM_STR_ARRAY)
                )
            )
            ->executeQuery()
            ->fetchOne();
            return true;

    }
    

    /**
     * Returns an array of class names of prerequisite classes
     *
     * This way a wizard can define dependencies like "database up-to-date" or
     * "reference index updated"
     *
     * @return string[]
     */
    public function getPrerequisites(): array
    {
        return [];
    }
}
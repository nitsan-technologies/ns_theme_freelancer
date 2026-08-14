<?php

declare(strict_types=1);

namespace NITSAN\NsThemeFreelancer\Initialization;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Finder\Finder;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Configuration\Exception\SiteConfigurationWriteException;
use TYPO3\CMS\Core\Configuration\SiteConfiguration;
use TYPO3\CMS\Core\Configuration\SiteWriter;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Package\Event\PackageInitializationEvent;
use TYPO3\CMS\Core\Package\Initialization\ImportExtensionDataOnPackageInitialization;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Impexp\Utility\ImportExportUtility;

/**
 * Imports demo page tree only on an empty installation.
 *
 * Demo XML lives in Initialisation/Demo/ so core's ImportContentOnPackageInitialization
 * never auto-imports it (that path only looks for Initialisation/data.xml).
 * Existing page trees are never touched.
 */
final class ImportDemoContentOnPackageInitialization implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private const EXTENSION_KEY = 'ns_theme_freelancer';
    private const REGISTRY_NAMESPACE = 'extensionDataImport';
    private const REGISTRY_KEY_SUFFIX = 'Initialisation/Demo/dataImported';

    public function __construct(
        private readonly Registry $registry,
        private readonly ConnectionPool $connectionPool,
        private readonly ImportExportUtility $importExportUtility,
        private readonly SiteConfiguration $siteConfiguration,
        private readonly SiteWriter $siteWriter,
        private readonly ResourceFactory $resourceFactory,
    ) {}

    #[AsEventListener(
        identifier: 'ns-theme-freelancer/import-demo-content',
        after: ImportExtensionDataOnPackageInitialization::class,
    )]
    public function __invoke(PackageInitializationEvent $event): void
    {
        if ($event->getExtensionKey() !== self::EXTENSION_KEY) {
            return;
        }

        $packagePath = $event->getPackage()->getPackagePath();
        $registryKey = $this->buildRegistryKey($packagePath);

        if ($this->registry->get(self::REGISTRY_NAMESPACE, $registryKey)) {
            return;
        }

        $importFile = $packagePath . 'Initialisation/Demo/data.xml';
        if (!is_file($importFile)) {
            return;
        }

        $pageCount = $this->connectionPool
            ->getConnectionForTable('pages')
            ->count('uid', 'pages', ['deleted' => 0]);

        // Existing tree: mark done and never import/overwrite.
        if ($pageCount > 0) {
            $this->registry->set(self::REGISTRY_NAMESPACE, $registryKey, 1);
            $this->logger?->info(
                'Skipped ns_theme_freelancer demo import because {count} page(s) already exist.',
                ['count' => $pageCount]
            );
            return;
        }

        Bootstrap::initializeBackendAuthentication();

        try {
            $importResult = $this->importExportUtility->importT3DFile($importFile, 0);
            $this->registry->set(self::REGISTRY_NAMESPACE, $registryKey, 1);
            $this->importSiteConfiguration($event, $packagePath);
            $this->remapImportedFormPersistenceIdentifiers();
            $event->addStorageEntry(__CLASS__, [
                'importResult' => $importResult,
                'importFile' => $importFile,
            ]);
        } catch (\Throwable $e) {
            $this->logger?->warning(
                'ns_theme_freelancer demo import failed: {message}',
                ['message' => $e->getMessage(), 'exception' => $e]
            );
        }
    }

    private function buildRegistryKey(string $packagePath): string
    {
        return PathUtility::stripPathSitePrefix($packagePath) . self::REGISTRY_KEY_SUFFIX;
    }

    private function importSiteConfiguration(PackageInitializationEvent $event, string $packagePath): void
    {
        $importAbsFolder = $packagePath . 'Initialisation/Site';
        if (!is_dir($importAbsFolder)) {
            return;
        }

        $destinationFolder = Environment::getConfigPath() . '/sites';
        GeneralUtility::mkdir($destinationFolder);
        $existingSites = $this->siteConfiguration->resolveAllExistingSites(false);

        $finder = GeneralUtility::makeInstance(Finder::class);
        $finder->directories()->ignoreUnreadableDirs()->depth(0)->in($importAbsFolder);
        if (!$finder->hasResults()) {
            return;
        }

        foreach ($finder as $siteConfigDirectory) {
            $siteIdentifier = $siteConfigDirectory->getBasename();
            if (isset($existingSites[$siteIdentifier])) {
                $this->logger?->warning(
                    'Skipped importing site configuration {site} because it already exists.',
                    ['site' => $siteIdentifier]
                );
                continue;
            }
            $targetDir = $destinationFolder . '/' . $siteIdentifier;
            if ($this->registry->get('siteConfigImport', $siteIdentifier) || is_dir($targetDir)) {
                continue;
            }
            GeneralUtility::mkdir($targetDir);
            GeneralUtility::copyDirectory($siteConfigDirectory->getPathname(), $targetDir);
            $this->registry->set('siteConfigImport', $siteIdentifier, 1);
        }

        $import = $this->importExportUtility->getImport();
        if ($import === null) {
            return;
        }

        $importedPages = $import->getImportMapId()['pages'] ?? [];
        $newSites = array_diff_key(
            $this->siteConfiguration->resolveAllExistingSites(false),
            $existingSites
        );

        foreach ($newSites as $newSite) {
            $exportedPageId = $newSite->getRootPageId();
            $importedPageId = $importedPages[$exportedPageId] ?? null;
            if ($importedPageId === null) {
                continue;
            }
            $configuration = $this->siteConfiguration->load($newSite->getIdentifier());
            $configuration['rootPageId'] = $importedPageId;
            try {
                $this->siteWriter->write($newSite->getIdentifier(), $configuration);
            } catch (SiteConfigurationWriteException $e) {
                $this->logger?->warning(
                    'Could not write site configuration {site}: {message}',
                    [
                        'site' => $newSite->getIdentifier(),
                        'message' => $e->getMessage(),
                    ]
                );
            }
        }
    }

    /**
     * TYPO3 impexp remaps form persistence identifiers to FAL "storage:uid"
     * (e.g. "1:3"). EXT:form in v14 only loads YAML path identifiers
     * (e.g. "1:/ns_theme_freelancer/Forms/contactForm.form.yaml").
     */
    private function remapImportedFormPersistenceIdentifiers(): void
    {
        $connection = $this->connectionPool->getConnectionForTable('tt_content');
        $records = $connection->select(
            ['uid', 'pi_flexform'],
            'tt_content',
            ['CType' => 'form_formframework', 'deleted' => 0]
        )->fetchAllAssociative();

        foreach ($records as $record) {
            $flexform = (string)($record['pi_flexform'] ?? '');
            if ($flexform === '') {
                continue;
            }

            $updatedFlexform = $this->replaceLegacyFormPersistenceIdentifier($flexform);
            if ($updatedFlexform === $flexform) {
                continue;
            }

            $connection->update(
                'tt_content',
                ['pi_flexform' => $updatedFlexform],
                ['uid' => (int)$record['uid']]
            );
        }
    }

    private function replaceLegacyFormPersistenceIdentifier(string $flexform): string
    {
        if (!preg_match(
            '#(<field index="settings\.persistenceIdentifier">\s*<value index="vDEF">)([^<]+)(</value>)#s',
            $flexform,
            $matches
        )) {
            return $flexform;
        }

        $identifier = html_entity_decode(trim($matches[2]), ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $pathIdentifier = $this->resolveYamlFormPersistenceIdentifier($identifier);
        if ($pathIdentifier === null || $pathIdentifier === $identifier) {
            return $flexform;
        }

        return str_replace(
            $matches[0],
            $matches[1] . htmlspecialchars($pathIdentifier, ENT_XML1) . $matches[3],
            $flexform
        );
    }

    private function resolveYamlFormPersistenceIdentifier(string $identifier): ?string
    {
        if (!preg_match('/^(\d+):(\d+)$/', $identifier, $matches)) {
            return null;
        }

        $storageUid = (int)$matches[1];
        $fileUid = (int)$matches[2];

        try {
            $file = $this->resourceFactory->getFileObject($fileUid);
        } catch (FileDoesNotExistException) {
            return null;
        }

        if (!$file instanceof File
            || $file->getStorage()->getUid() !== $storageUid
            || $file->getExtension() !== 'yaml'
        ) {
            return null;
        }

        return $file->getCombinedIdentifier();
    }
}

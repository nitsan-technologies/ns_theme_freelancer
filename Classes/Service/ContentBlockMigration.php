<?php

namespace  NITSAN\NsThemeFreelancer\Service;

use SimpleXMLElement;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\LinkHandling\LinkService;
use NITSAN\NsThemeFreelancer\Domain\Repository\ContentBlocksRepository;


class ContentBlockMigration
{
    private $contentBlocksRepository;

    private $connectionPool;

    public function __construct()
    {
        $this->contentBlocksRepository = GeneralUtility::makeInstance(ContentBlocksRepository::class);
        $this->connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
    }

    public function migrate(array $elements)
    {
        foreach ($elements as $ce) {
            $cType = 'nitsan_' . str_replace('_', '', $ce);
            $flexFormPath = GeneralUtility::getFileAbsFileName('EXT:ns_theme_freelancer/Configuration/FlexForms/');
            $originalXml = $this->scanAndParseXmlFiles($flexFormPath, $ce);
            $fields = $this->getFieldsFromXml($originalXml);
            $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
            $registeredContentElements = $this->contentBlocksRepository->getRegisteredContentElements($ce);
            if (!empty($registeredContentElements)) {
                foreach ($registeredContentElements as $element) {
                    if (isset($element['pi_flexform']) && $element['pi_flexform'] !== '') {
                        $pid = $element['pid'];
                        $uid = $element['uid'];
                        $langUid = $element['sys_language_uid'];
                        $xml = simplexml_load_string($element['pi_flexform']);
                        $parsed = [];

                        if (isset($xml->data->sheet)) {
                            foreach ($xml->data->sheet as $sheet) {
                                $fields = $sheet->language->field ?? null;
                                if ($fields) {
                                    $parsed = array_merge_recursive($parsed, $this->parseFields($fields));
                                }
                            }
                        }
                        $this->migrateFlexForm(
                            $uid,
                            $pid,
                            $cType,
                            $parsed,
                            $langUid
                        );
                    }
                }
            }
        }
    }

    private function parseFields($fields)
    {
        $result = [];

        foreach ($fields as $field) {
            $key = (string) $field['index'];

            // If it has a direct value (simple case)
            if (isset($field->value) && (string) $field->value['index'] === 'vDEF') {
                $result[$key] = html_entity_decode((string) $field->value);
            }

            // If it has nested <el> content
            if (isset($field->el)) {
                foreach ($field->el->field as $nestedField) {
                    $nestedKey = (string) $nestedField['index'];
                    $container = $nestedField->value['index'] == 'container' ? $nestedField->value->el->field : null;
                    try {
                        if ($container) {
                            // Ensure $result[$key] is an array before assigning to $result[$key][$nestedKey]
                            if (!isset($result[$key]) || !is_array($result[$key])) {
                                $result[$key] = [];
                            }

                            $result[$key][$nestedKey] = $this->parseFields($container);
                        }
                    } catch (\Exception $e) {
                        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($result, __FILE__ . ' ' . __LINE__);
                    }
                }
            }
        }

        return $result;
    }

    private function getFieldsFromXml(SimpleXMLElement $xml): array
    {
        $fields = [];
        if ($xml) {
            $elElements = $xml->xpath('//el')[0];
            foreach ($elElements as $elementName => $elementData) {

                $fieldType = (string)$elementData->config->type;
                if ($fieldType === '') {
                    $fieldType = 'Text';
                }

                if ($fieldType || ($elementData->section)) {

                    if ($elementData->el->container) {

                        $collectionData = [
                            'identifier' => $elementName,
                            'label' => (string)trim($elementData->title),
                            'type' => 'Collection',
                        ];

                        $containerElements = $elementData->xpath('el/container/el')[0];

                        foreach ($containerElements as $celementName => $celementData) {
                            $fieldType = (string)$celementData->config->type;
                            if ($fieldType === '') {
                                $fieldType = 'Text';
                            }
                            $fieldData = $this->extractFieldProperties($celementData, $fieldType, $celementName);
                            $collectionData['fields'][] = $fieldData;
                        }

                        $fields[] = $collectionData;
                    } else {

                        $fieldData = $this->extractFieldProperties($elementData, $fieldType, $elementName);
                        $fields[] = $fieldData;
                    }
                }
            }
        }
        return $fields;
    }

    private function handleFieldTypes(string $fieldType): array
    {
        return match ($fieldType) {
            'input' => ['type' => 'Text'],
            'text' => ['type' => 'Textarea'],
            'check' => ['type' => 'Checkbox'],
            'link' => ['type' => 'Link'],
            'select' => ['type' => 'Select'],
            default => [],
        };
    }

    private function scanAndParseXmlFiles(string $directory, string $targetFile): ?SimpleXMLElement
    {
        $files = scandir($directory);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'xml' && $file === $targetFile . '.xml') {
                $xmlContent = file_get_contents($directory . DIRECTORY_SEPARATOR . $file);
                return simplexml_load_string($xmlContent) ?: null;
            }
        }
        return null;
    }

    private function extractFieldProperties(SimpleXMLElement $elementData, string $fieldType, string $fieldName): array
    {
        $fieldData = [
            'identifier' => $fieldName,
            'label' => (string)trim($elementData->label ?? $elementData->title),
        ];

        if ($fieldType) {
            $fieldData = array_merge($fieldData, $this->handleFieldTypes($fieldType));
        }
        if (!empty($elementData->onChange)) {
            $fieldData['onChange'] = (string)$elementData->onChange;
        }
        if (!empty($elementData->itemsProcFunc)) {
            $fieldData['itemsProcFunc'] = (string)$elementData->itemsProcFunc;
        }
        if (!empty($elementData->displayCond)) {
            $condition = (string)$elementData->displayCond;
            $condition = str_replace('sDEF.', '', $condition);
            if ($condition !== 'AND' || $condition !== 'OR') {
                $fieldData['displayCond']['AND'] = [$condition];
            }
            $fieldData['size'] = (int)$elementData->config->size;
        }
        $renderType = (string)$elementData->config->renderType;

        if ($fieldType === 'number' || $fieldType === 'Number') {
            $fieldData['type'] = 'Number';
        }

        if ($fieldType === 'input' && $renderType) {

            switch ($renderType) {
                case 'inputLink':
                    $fieldData['type'] =  'Link';
                    break;

                case 'colorpicker':
                    $fieldData['type'] =  'Color';
                    break;

                case 'inputDateTime':
                    $fieldData['type'] =  'DateTime';
                    break;
            }
        }

        if ($fieldType === 'inline') {
            $foreigntable = (string)$elementData->config->foreign_table;
            if ($foreigntable === 'sys_file_reference') {
                $fieldData['type'] = 'File';
                $allowedTypes = '*';
                if (!empty($elementData->config->overrideChildTca->columns)) {
                    $allowedTypes =  (string)$elementData->config->overrideChildTca->columns->uid_local->config->appearance->elementBrowserAllowed;
                }
                if ($allowedTypes) {
                    $allowedTypes = GeneralUtility::trimExplode(',', $allowedTypes);
                    foreach ($allowedTypes as $type) {
                        $fieldData['allowed'][] = $type;
                    }
                }
            }
        }

        if ($fieldType === 'text') {
            $fieldData['rows'] = isset($elementData->config->rows) ? (int)(string)$elementData->config->rows : null;
            $fieldData['enableRichtext'] = !empty($elementData->config->enableRichtext)
                ? filter_var($elementData->config->enableRichtext, FILTER_VALIDATE_BOOLEAN)
                : false;
        }

        if ($fieldType === 'select') {
            $fieldData['renderType'] = (string)$renderType;
            $items = (array)$elementData->config->items;

            if ($items) {
                $items = $items['numIndex'];
                foreach ($items as $option) {
                    $fieldData['items'][] = [
                        'label' => (string)$option->numIndex[0],
                        'value' => (string)$option->numIndex[1],
                    ];
                }
            }
        }

        $validations = (string)$elementData->config->eval;

        if ($validations) {
            $fieldData = array_merge($fieldData, $this->handleValidations($validations));
        }

        if ($elementData->config->allowedTypes && $elementData->config->allowedTypes->numIndex) {

            $allowedTypes = (array)$elementData->config->allowedTypes->numIndex;

            if ($allowedTypes) {
                foreach ($allowedTypes as $type) {
                    if (is_string($type)) {
                        $fieldData['allowed'][] = $type;
                    }
                }
            }
        }

        $minitems = (int)$elementData->config->minitems;
        if ($minitems > 0) {
            $fieldData['minitems'] = $minitems;
        }

        $maxitems = (int)$elementData->config->maxitems;
        if ($maxitems > 0) {
            $fieldData['maxitems'] = $maxitems;
        }

        return $fieldData;
    }


    private function handleValidations(string $validations): array
    {
        $validationArray = GeneralUtility::trimExplode(',', $validations);
        $validationFields = [];

        foreach ($validationArray as $validation) {
            switch ($validation) {
                case 'required':
                    $validationFields['required'] = true;
                    break;
            }
        }

        return $validationFields;
    }


    public function migrateFlexForm(
        $uid,
        $pid,
        $cType,
        $parsed,
        $langUid
    ) {

        switch ($cType) {
            case 'nitsan_nsbanner':
                $this->migrateBanner($uid, $pid, $cType, $parsed, $langUid);
                break;
            case 'nitsan_nsportfolio':
                $this->migratePortfolio($uid, $pid, $cType, $parsed, $langUid);
                break;
            case 'nitsan_nsaboutus':
                $this->migrateAboutUs($uid, $pid, $cType, $parsed, $langUid);
                break;
            case 'nitsan_nssocial':
                $this->migrateSocial($uid, $pid, $cType, $parsed, $langUid);
                break;
            default:
                break;
        }
    }
    
    private function migrateBanner($uid, $pid, $cType, $parsed, $langUid)
    {
        $data = [
            'CType' => $cType,
            'headline' => $parsed['headline'] ?? '',
            'text' => $parsed['text'] ?? '',
            'sys_language_uid' => $langUid,
        ];

        $this->updateTtContent($data, $uid, $pid);
    }

 private function migratePortfolio(int $uid, int $pid, string $cType, array $parsed, int $langUid): void
{
    $data = [
        'CType'              => $cType,
        'headline'           => $parsed['headline'] ?? '',
        'title'              => $parsed['title'] ?? '', 
        'portfolio'          => isset($parsed['portfolio']) ? count($parsed['portfolio']) : 0,
        'sys_language_uid'   => $langUid,
    ];

    $this->updateTtContent($data, $uid, $pid);

    if (!empty($parsed['portfolio'])) {
        foreach ($parsed['portfolio'] as $portfolio) {
            $randomString = StringUtility::getUniqueId('NEW');
            $imagePath = $portfolio['image'] ?? '';
            if (!str_starts_with($imagePath, 't3://file')) {
                $imagePath = 't3://file?uid=' . (int)$imagePath;
            }

            $itemData = [
                'pid'                      => $pid,
                'foreign_table_parent_uid' => $uid,
                'sys_language_uid'         => $langUid,
                'headline'                 => $portfolio['headline'] ?? '',
                'description'              => $portfolio['description'] ?? '',
                'image'                    => $imagePath,
            ];

            $this->contentBlocksRepository->insertDataWithDataHandler($itemData, $randomString, 'portfolio');
        }
    }
}

    private function migrateAboutUs($uid, $pid, $cType, $parsed, $langUid)
    {
        $data = [
            'CType' => $cType,
            'title' => $parsed['title'] ?? '',
            'lefttxt' => $parsed['lefttxt'] ?? '',
            'righttxt' => $parsed['righttxt'] ?? '',
            'btntxt' => $parsed['btntxt'] ?? '',
            'btnlink' => $parsed['btnlink'] ?? '',
            'sys_language_uid' => $langUid,
        ];

        $this->updateTtContent($data, $uid, $pid);
    }
    private function migrateSocial($uid, $pid, $cType, $parsed, $langUid): void
    {
            $linkService = GeneralUtility::makeInstance(LinkService::class);

            $links = [
                'title' => $parsed['title'] ?? '',
                'fblink' => $parsed['fblink'] ?? '',
                'twlink' => $parsed['twlink'] ?? '',
                'inlink' => $parsed['inlink'] ?? '',
                'drilink' => $parsed['drilink'] ?? '',
            ];

            // Convert TypolinkParameter objects to string URLs
            foreach ($links as $key => $link) {
                if ($link instanceof \TYPO3\CMS\Core\LinkHandling\TypolinkParameter) {
                    $links[$key] = $linkService->resolve($link)->getAbsoluteUri();
                }
            }

            $data = [
                'CType' => $cType,
                'title' => $parsed['title'] ?? '',
                'fblink' => $links['fblink'],
                'twlink' => $links['twlink'],
                'inlink' => $links['inlink'],
                'drilink' => $links['drilink'],
                'sys_language_uid' => $langUid,
            ];

            $this->updateTtContent($data, $uid, $pid);
    }

      private function updateTtContent($data, $uid, $pid)
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
        $queryBuilder
            ->update('tt_content')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid))
            )
            ->andWhere(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid))
            );
        foreach ($data as $key => $val) {
            $queryBuilder->set($key, $val);
        }
        $queryBuilder->executeStatement();
    }


}
<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Admin\Service;

use DOMDocument;
use DOMElement;
use DOMException;
use Exception;
use LogicException;
use MessageFormatter;
use IntlException;
use OrangeHRM\Admin\Dao\LocalizationDao;
use OrangeHRM\Admin\Dto\I18NGroupSearchFilterParams;
use OrangeHRM\Admin\Dto\I18NImportErrorSearchFilterParams;
use OrangeHRM\Admin\Dto\I18NLanguageSearchFilterParams;
use OrangeHRM\Admin\Dto\I18NTranslationSearchFilterParams;
use OrangeHRM\Admin\Exception\XliffFileProcessFailedException;
use OrangeHRM\Admin\Service\Model\I18NLanguageModel;
use OrangeHRM\Core\Dto\Base64Attachment;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Entity\I18NError;
use OrangeHRM\Entity\I18NGroup;
use OrangeHRM\Entity\I18NImportError;
use OrangeHRM\Entity\I18NLanguage;
use OrangeHRM\Entity\I18NTranslation;
use OrangeHRM\Framework\Services;
use OrangeHRM\I18N\Service\I18NService;
use OrangeHRM\Installer\Util\SystemConfig\SystemConfiguration;
use Symfony\Component\Translation\Util\XliffUtils;

class LocalizationService
{
    use NormalizerServiceTrait;
    use DateTimeHelperTrait;
    use ConfigServiceTrait;
    use ServiceContainerTrait;

    public const PLACEHOLDER_PATTERN = '/{(\w+)}|\s?(\w+)\s?,\s?plural|\s?(\w+)\s?,\s?select/';
    public const PLURAL_PATTERN = '/\s?(\w+)\s?,\s?plural/';
    public const SELECT_PATTERN = '/\s?(\w+)\s?,\s?select/';
    public const PATTERN_ERROR_MAP = [
        self::SELECT_PATTERN => I18NError::SELECT_MISMATCH,
        self::PLURAL_PATTERN => I18NError::PLURAL_MISMATCH,
        self::PLACEHOLDER_PATTERN => I18NError::PLACEHOLDER_MISMATCH,
    ];

    /**
     * @var LocalizationDao|null
     */
    private ?LocalizationDao $localizationDao = null;

    /**
     * @return LocalizationDao
     */
    public function getLocalizationDao(): LocalizationDao
    {
        if (!$this->localizationDao instanceof LocalizationDao) {
            $this->localizationDao = new LocalizationDao();
        }
        return $this->localizationDao;
    }

    /**
     * @return I18NService
     */
    private function getI18NService(): I18NService
    {
        return $this->getContainer()->get(Services::I18N_SERVICE);
    }

    /**
     * @return string[]
     */
    public function getLocalizationDateFormats(): array
    {
        $date = $this->getDateTimeHelper()->getNow();
        $dateFormats = [];
        foreach ($this->getSupportedDateFormats() as $format => $label) {
            $dateFormats[] = [
                'id' => $format,
                'label' => "$label ( " . (clone $date)->format($format) . ' )',
            ];
        }
        return $dateFormats;
    }

    /**
     * @return string[]
     */
    public function getSupportedDateFormats(): array
    {
        return [
            'Y-m-d' => 'yyyy-mm-dd',
            'd-m-Y' => 'dd-mm-yyyy',
            'm-d-Y' => 'mm-dd-yyyy',
            'Y-d-m' => 'yyyy-dd-mm',
            'm-Y-d' => 'mm-yyyy-dd',
            'd-Y-m' => 'dd-yyyy-mm',
            'Y/m/d' => 'yyyy/mm/dd',
            'Y m d' => 'yyyy mm dd',
            'Y-M-d' => 'yyyy-M-dd',
            'l, d-M-Y' => 'DD, dd-M-yyyy',
            'D, d M Y' => 'D, dd M yyyy'
        ];
    }

    /**
     * @return array
     */
    public function getCurrentDateFormat(): array
    {
        $dateFormat = $this->getConfigService()->getAdminLocalizationDefaultDateFormat();
        return [
            'id' => $dateFormat,
            'label' => $this->getSupportedDateFormats()[$dateFormat],
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getSupportedLanguages(): array
    {
        $i18NLanguageSearchParams = new I18NLanguageSearchFilterParams();
        $i18NLanguageSearchParams->setAddedOnly(true);
        $i18NLanguageSearchParams->setEnabledOnly(true);

        return $this->getLanguagesArray($i18NLanguageSearchParams);
    }

    /**
     * @param I18NLanguageSearchFilterParams $i18NLanguageSearchParams
     * @return array
     */
    public function searchLanguages(I18NLanguageSearchFilterParams $i18NLanguageSearchParams): array
    {
        return $this->getLocalizationDao()->searchLanguages($i18NLanguageSearchParams);
    }

    /**
     * @param I18NLanguageSearchFilterParams $i18NLanguageSearchParams
     * @return array
     */
    public function getLanguagesArray(I18NLanguageSearchFilterParams $i18NLanguageSearchParams): array
    {
        $languages = $this->searchLanguages($i18NLanguageSearchParams);
        return $this->getNormalizerService()->normalizeArray(I18NLanguageModel::class, $languages);
    }

    /**
     * @param int $languageId
     * @param array $rows e.g. [['langStringId'=> 1, 'translatedValue' => 'Employee'], ['langStringId'=> 2, 'translatedValue' => 'Admin']]
     */
    public function saveAndUpdateTranslatedStringsFromRows(int $languageId, array $rows): void
    {
        $language = $this->getLocalizationDao()->getLanguageById($languageId);
        $i18NTranslations = $this->createTranslatedItemsFromRows($languageId, $rows);
        $this->getLocalizationDao()->saveAndUpdateTranslatedLangString($i18NTranslations);
        $this->getI18NService()->cleanCacheByLangCode($language->getCode());
    }

    /**
     * @param int $languageId
     * @param int $empNumber
     * @param array $rows
     */
    public function saveImportErrorLangStringsFromRows(int $languageId, int $empNumber, array $rows): void
    {
        $i18NImportErrors = $this->createImportErrorItemsFromRows(
            $languageId,
            $empNumber,
            $rows
        );
        $this->getLocalizationDao()->saveImportErrorLangStrings($i18NImportErrors);
    }

    /**
     * @param int $languageId
     * @param array $rows
     * @return I18NTranslation[]
     */
    protected function createTranslatedItemsFromRows(int $languageId, array $rows): array
    {
        $i18NTranslations = [];
        foreach ($rows as $row) {
            if (!(isset($row['langStringId']))) {
                throw new LogicException('langStringId is required attribute');
            }

            $itemKey = $this->generateLangStringLanguageKey(
                $languageId,
                $row['langStringId'],
            );
            $i18NTranslation = new I18NTranslation();
            $i18NTranslation->getDecorator()->setLangStringById($row['langStringId']);
            $i18NTranslation->getDecorator()->setLanguageById($languageId);
            $i18NTranslation->setValue(
                empty($row['translatedValue']) ? null : $row['translatedValue']
            );
            $i18NTranslation->setCustomized(true);
            $i18NTranslation->setModifiedAt($this->getDateTimeHelper()->getNow());
            $i18NTranslations[$itemKey] = $i18NTranslation;
        }
        return $i18NTranslations;
    }

    /**
     * @param int $languageId
     * @param int $empNumber
     * @param array $rows
     * @return array<string, I18NImportError>
     */
    private function createImportErrorItemsFromRows(int $languageId, int $empNumber, array $rows): array
    {
        $i18NImportErrors = [];
        foreach ($rows as $row) {
            if (!(isset($row['langStringId'])) || !(isset($row['errorName']))) {
                throw new LogicException('langStringId and errorName are required');
            }
            $importError = new I18NImportError();
            $importError->getDecorator()->setLangStringById($row['langStringId']);
            $importError->getDecorator()->setErrorByName($row['errorName']);
            $importError->getDecorator()->setLanguageById($languageId);
            $importError->getDecorator()->setImportedEmployeeByEmpNumber($empNumber);

            $i18NImportErrors[] = $importError;
        }

        return $i18NImportErrors;
    }

    /**
     * @param int $languageId
     * @param int $langStringId
     * @return string
     */
    public function generateLangStringLanguageKey(int $languageId, int $langStringId): string
    {
        return $languageId . '_' .
            $langStringId . '_';
    }

    /**
     * @param I18NLanguage $language
     * @return string
     */
    public function exportLanguagePackage(I18NLanguage $language): string
    {
        $i18NGroupSearchFilterParams = new I18NGroupSearchFilterParams();
        $i18nGroups = $this->getLocalizationDao()->searchGroups($i18NGroupSearchFilterParams);
        $i18nSources = $this->getXliffXmlSources($i18nGroups, $language);

        return $i18nSources->saveXML();
    }

    /**
     * @param I18NGroup[] $i18nGroups
     * @param I18NLanguage $language
     * @return DOMDocument
     * @throws DOMException
     */
    private function getXliffXmlSources(array $i18nGroups, I18NLanguage $language): DOMDocument
    {
        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        $root = $xml->createElement('xliff');
        $xml->appendChild($root);
        $root->setAttribute('version', '2.0');
        $root->setAttribute('srcLang', 'en_US');
        $root->setAttribute('trgLang', $language->getCode());
        $root->setAttribute('xmlns', 'urn:oasis:names:tc:xliff:document:2.0');

        $file = $xml->createElement('file');
        $file->setAttribute('id', 1);
        $root->appendChild($file);

        foreach ($i18nGroups as $i18nGroup) {
            if ($i18nGroup instanceof I18NGroup) {
                $i18NTargetLangStringSearchFilterParams
                    = new I18NTranslationSearchFilterParams();
                $i18NTargetLangStringSearchFilterParams->setLanguageId($language->getId());
                $i18NTargetLangStringSearchFilterParams->setLimit(0);
                $i18NTargetLangStringSearchFilterParams->setGroupId($i18nGroup->getId());
                $translations = $this->localizationDao->getNormalizedTranslationsForExport(
                    $i18NTargetLangStringSearchFilterParams
                );

                $group = $xml->createElement('group');
                $file->appendChild($group);
                $group->setAttribute('id', $i18nGroup->getName());


                foreach ($translations as $translation) {
                    $unit = $xml->createElement('unit');
                    $group->appendChild($unit);
                    $unit->setAttribute('id', $translation['unitId']);

                    $segment = $xml->createElement('segment');
                    $unit->appendChild($segment);

                    $source = $xml->createElement('source');
                    $target = $xml->createElement('target');

                    $source->appendChild(new \DOMText($translation['source']));
                    $target->appendChild(new \DOMText($translation['target'] ?? ''));

                    $segment->appendChild($source);
                    $segment->appendChild($target);
                }
            }
        }
        return $xml;
    }

    /**
     * @param DOMDocument $xliffContents
     * @return string|null
     */
    private function getTargetLanguageFromFile(DOMDocument $xliffContents): ?string
    {
        $xliffTags = $xliffContents->getElementsByTagName('xliff');
        if ($xliffTags->length > 0) {
            $xliffTag = $xliffTags->item(0);
            if ($xliffTag && $xliffTag->hasAttribute('trgLang')) {
                return $xliffTag->getAttribute('trgLang');
            }
        }

        return null;
    }

    /**
     * @param int $langStringId
     * @param string $targetValue
     * @return I18NError|null
     */
    public function validateTargetString(int $langStringId, string $targetValue): ?I18NError
    {
        try {
            new MessageFormatter(
                SystemConfiguration::DEFAULT_LANGUAGE,
                $targetValue
            );
        } catch (IntlException $exception) {
            return $this->getI18NErrorByName(I18NError::INVALID_SYNTAX);
        }

        $sourceValue = $this->getLocalizationDao()
            ->getLangStringById($langStringId)
            ->getValue();

        $sourceMatches = [];
        $targetMatches = [];
        foreach (self::PATTERN_ERROR_MAP as $pattern => $error) {
            preg_match_all($pattern, $sourceValue, $sourceMatches);
            preg_match_all($pattern, $targetValue, $targetMatches);

            $capturedSource = $sourceMatches[1];
            $capturedTarget = $targetMatches[1];

            if ($pattern === self::PLACEHOLDER_PATTERN) {
                // If the source string has plural or select, skip checking for placeholders
                if (array_filter($sourceMatches[2]) || array_filter($sourceMatches[3])) {
                    continue;
                }

                // Sort the matches to allow placeholders to be in any order
                sort($capturedSource);
                sort($capturedTarget);
            }

            if ($capturedSource !== $capturedTarget) {
                return $this->getI18NErrorByName($error);
            }
        }

        return null;
    }

    /**
     * @param string $name
     * @return I18NError
     */
    public function getI18NErrorByName(string $name): I18NError
    {
        if (!in_array($name, I18NError::ERROR_MAP)) {
            throw new LogicException("Should be a valid I18N error");
        }
        return $this->getLocalizationDao()->getI18NErrorByName($name);
    }

    /**
     * Validate Xliff file and return an array of valid and failed lang strings
     *
     * @param Base64Attachment $attachment
     * @param int $languageId
     * @return array
     * @throws XliffFileProcessFailedException
     */
    public function processXliffFile(Base64Attachment $attachment, int $languageId): array
    {
        $this->validateXliffFile(
            $attachment->getContent(),
            $this->getLocalizationDao()->getLanguageById($languageId)->getCode()
        );

        return $this->validateLangStrings($attachment->getContent());
    }

    /**
     * @param string $content
     * @param string $langCode
     * @throws XliffFileProcessFailedException
     */
    private function validateXliffFile(string $content, string $langCode): void
    {
        if ('' === trim($content)) {
            throw XliffFileProcessFailedException::emptyFile();
        }

        // Enable this to check errors
        $internal = libxml_use_internal_errors(true);

        $document = new \DOMDocument();
        $document->loadXML($content);

        $version = !is_null($document->getElementsByTagName('xliff')[0]) ?
            $document->getElementsByTagName('xliff')[0]->getAttribute("version") :
            "";

        if ($version !== "2.0" || count(XliffUtils::validateSchema($document)) > 0) {
            throw XliffFileProcessFailedException::validationFailed();
        }

        $targetLanguage = $this->getTargetLanguageFromFile($document);

        if (is_null($targetLanguage)) {
            throw XliffFileProcessFailedException::missingTargetLanguage();
        }

        if ($targetLanguage !== $langCode) {
            throw XliffFileProcessFailedException::invalidTargetLanguage();
        }

        libxml_clear_errors();
        libxml_use_internal_errors($internal);
    }

    /**
     * Get an array of valid and invalid lang strings
     *
     * @param string $content
     * @return array
     */
    private function validateLangStrings(string $content): array
    {
        $xliffDocument = new DOMDocument('1.0', 'UTF-8');
        $xliffDocument->loadXML($content);

        $validTargetStrings = [];
        $invalidTargetStrings = [];
        $skippedLangStrings = [];

        $units = $xliffDocument->getElementsByTagName('unit');

        /** @var DOMElement $unit */
        foreach ($units as $unit) {
            $unitId = $unit->getAttribute('id');
            $groupName  = $unit->parentNode->getAttribute('id');
            $group = $groupName ? $this->getLocalizationDao()->getI18NGroupByName($groupName) : null;

            // If a unit does not have an ID or a group, ignore it
            if (is_null($unitId) || is_null($group)) {
                continue;
            }

            $langString = $this->getLocalizationDao()->getLangStringByUnitIdAndGroupId($unitId, $group->getId());

            // If there is no corresponding lang string in the system, ignore it
            if (is_null($langString)) {
                continue;
            }

            $target = $unit->getElementsByTagName('target')->item(0)->nodeValue;

            // If the target string is empty, ignore it
            if (is_null($target) || "" === $target) {
                $skippedLangStrings[] = [
                    'langStringId' => $langString->getId(),
                    'unitId' => $langString->getUnitId()
                ];
                continue;
            }

            $error = $this->validateTargetString($langString->getId(), $target);

            if (is_null($error)) {
                $validTargetStrings[] = [
                    'langStringId' => $langString->getId(),
                    'translatedValue' => $target
                ];
            } else {
                $invalidTargetStrings[] = [
                    'langStringId' => $langString->getId(),
                    'errorName' => $error->getName(),
                ];
            }
        }

        return [
            $validTargetStrings,
            $invalidTargetStrings,
            $skippedLangStrings
        ];
    }

    /**
     * @param int $languageId
     * @param int $empNumber
     * @return bool
     */
    public function languageHasImportErrors(int $languageId, int $empNumber): bool
    {
        $importErrorSearchFilterParams = new I18NImportErrorSearchFilterParams();
        $importErrorSearchFilterParams->setLanguageId($languageId);
        $importErrorSearchFilterParams->setEmpNumber($empNumber);

        return $this->getLocalizationDao()->getImportErrorCount($importErrorSearchFilterParams) > 0;
    }

    /**
     * @param int $languageId
     * @param array $rows
     */
    public function clearImportErrorsForLangStrings(int $languageId, array $rows): void
    {
        $langStringIds = array_column($rows, 'langStringId');
        $this->getLocalizationDao()->clearImportErrorsForLangStrings($languageId, $langStringIds);
    }
}

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
use Exception;
use OrangeHRM\Admin\Dao\LocalizationDao;
use OrangeHRM\Admin\Dto\I18NGroupSearchFilterParams;
use OrangeHRM\Admin\Dto\I18NLanguageSearchFilterParams;
use OrangeHRM\Admin\Dto\I18NTranslationSearchFilterParams;
use OrangeHRM\Admin\Service\Model\I18NLanguageModel;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Entity\I18NGroup;
use OrangeHRM\Entity\I18NLanguage;
use OrangeHRM\Entity\I18NTranslation;
use OrangeHRM\Framework\Services;
use OrangeHRM\I18N\Service\I18NService;

class LocalizationService
{
    use NormalizerServiceTrait;
    use DateTimeHelperTrait;
    use ConfigServiceTrait;
    use ServiceContainerTrait;

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
     * @return void
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
     * @param array $rows
     * @return I18NTranslation[]
     */
    protected function createTranslatedItemsFromRows(int $languageId, array $rows): array
    {
        $i18NTranslations = [];
        foreach ($rows as $row) {
            if (!(isset($row['langStringId']))) {
                throw new \LogicException('langStringId is required attribute');
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
     * @throws \DOMException
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
        $root->setAttribute('date', @date('Y-m-d\TH:i:s\Z'));

        $file = $xml->createElement('file');
        $root->appendChild($file);

        foreach ($i18nGroups as  $i18nGroup) {
            if ($i18nGroup instanceof I18NGroup) {
                $i18NTargetLangStringSearchFilterParams
                    = new I18NTranslationSearchFilterParams();
                $i18NTargetLangStringSearchFilterParams->setLanguageId($language->getId());
                $i18NTargetLangStringSearchFilterParams->setLimit(0);
                $i18NTargetLangStringSearchFilterParams->setGroupId($i18nGroup->getId());
                $translations = $this->localizationDao->getNormalizedTranslationsForExport($i18NTargetLangStringSearchFilterParams);

                $group = $xml->createElement('group');
                $file->appendChild($group);
                $group->setAttribute('name', $i18nGroup->getName());


                foreach ($translations as $translation) {
                    $unit = $xml->createElement('unit');
                    $group->appendChild($unit);
                    $unit->setAttribute('id', $translation['unitId']);

                    $segment = $xml->createElement('segment');
                    $unit->appendChild($segment);

                    $source = $xml->createElement('source');
                    $target = $xml->createElement('target');

                    $source->appendChild(new \DOMText(htmlspecialchars($translation['source'])));
                    $target->appendChild(new \DOMText(htmlspecialchars($translation['target'] ?? '')));

                    $segment->appendChild($source);
                    $segment->appendChild($target);
                }
            }
        }
        return $xml;
    }
}

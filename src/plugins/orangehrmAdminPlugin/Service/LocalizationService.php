<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Admin\Service;

use Exception;
use LogicException;
use OrangeHRM\Admin\Dao\LocalizationDao;
use OrangeHRM\Admin\Dto\I18NLanguageSearchFilterParams;
use OrangeHRM\Admin\Dto\I18NTranslationSearchFilterParams;
use OrangeHRM\Admin\Service\Model\I18NLanguageModel;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
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

    public function exportLanguagePackage(string $languageId)
    {
        $i18NTargetLangStringSearchFilterParams
            = new I18NTranslationSearchFilterParams();
        $i18NTargetLangStringSearchFilterParams->setLanguageId($languageId);
        $i18NTargetLangStringSearchFilterParams->setOnlyTranslated(true);
        $i18NTargetLangStringSearchFilterParams->setLimit(0);
        $translations = $this->localizationDao->getNormalizedTranslations(
            $i18NTargetLangStringSearchFilterParams
        );
        dump($translations);
    }
}

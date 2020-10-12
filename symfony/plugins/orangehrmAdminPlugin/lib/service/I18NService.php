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

class I18NService extends BaseService
{
    const DEV_LANG_PACK = 'zz_ZZ';

    /**
     * @var null|I18NDao
     */
    protected $i18nDao = null;

    /**
     * @return I18NDao
     */
    public function getI18NDao(): I18NDao
    {
        if (is_null($this->i18nDao)) {
            $this->i18nDao = new I18NDao();
        }
        return $this->i18nDao;
    }

    /**
     * @param I18NDao $i18nDao
     */
    public function setI18NDao(I18NDao $i18nDao)
    {
        $this->i18nDao = $i18nDao;
    }

    /**
     * @param string $langCode
     * @param bool $onlyCustomized
     * @return array|Doctrine_Collection|I18NTranslate[]|int
     * @throws DaoException
     */
    public function getMessages(string $langCode, bool $onlyCustomized = true)
    {
        return $this->getI18NDao()->getMessages($langCode, $onlyCustomized);
    }

    /**
     * @param string $langCode
     * @return Doctrine_Record|I18NLanguage
     * @throws DaoException
     */
    public function getLanguageByCode(string $langCode)
    {
        return $this->getI18NDao()->getLanguageByCode($langCode);
    }

    /**
     * @param bool $withDisabled
     * @return array|Doctrine_Collection|I18NLanguage[]
     * @throws DaoException
     */
    public function getLanguages(bool $withDisabled = false)
    {
        return $this->getI18NDao()->getLanguages($withDisabled);
    }

    /**
     * @param string $id
     * @return I18NLanguage|null
     * @throws DaoException
     */
    public function getLanguageById(string $id)
    {
        return $this->getI18NDao()->getLanguageById($id);
    }

    /**
     * @param string $langCode
     * @return array|Doctrine_Collection|I18NTranslate[]|int
     * @throws DaoException
     */
    public function getTranslationsByCode(string $langCode)
    {
        return $this->getMessages($langCode, false);
    }

    /**
     * Sync all XLIFF sources language string to database
     * @throws DaoException
     * @throws sfException
     */
    public function syncI18NSourcesLangStrings()
    {
        foreach ($this->getI18NSourceDirs() as $sourceDir) {
            $baseSource = $this->getLangPackPath($sourceDir);

            $i18nSource = $this->getI18NDao()->getI18NSource($baseSource);
            if ($i18nSource instanceof I18NSource) {
                // Source already exists
                $timestamp = filemtime($baseSource);
                if ($i18nSource->getModifiedAt() < date("Y-m-d H:i:s", $timestamp)) {
                    $this->syncI18NSourceLangStrings($baseSource);
                    // Update last sync time
                    $i18nSource->setModifiedAt(date("Y-m-d H:i:s"));
                    $this->getI18NDao()->saveI18NSource($i18nSource);
                }
            } else {
                // New source
                $newSource = new I18NSource();
                $newSource->setSource($baseSource);
                $newSource->setModifiedAt(date("Y-m-d H:i:s"));
                $this->getI18NDao()->saveI18NSource($newSource);

                $this->syncI18NSourceLangStrings($baseSource);
            }
        }
    }

    /**
     * Sync single XLIFF source to database
     * @param string $source
     * @throws DaoException
     */
    public function syncI18NSourceLangStrings(string $source)
    {
        $i18nSource = $this->getI18NDao()->getI18NSource($source);
        if ($i18nSource instanceof I18NSource) {
            $ids = $this->saveLangStringsFromSource(
                $source,
                $i18nSource->getId()
            );
            if (!is_null($ids)) {
                $this->deleteRemovedLangStringsFromDatabase($ids, $i18nSource->getId());
            }
        }
    }

    /**
     * @param string $sourceFile absolute location of XLIFF source
     * @param int $sourceId source id from database I18NSource
     * @return array|null
     * @throws Exception
     */
    public function saveLangStringsFromSource(string $sourceFile, int $sourceId)
    {
        $translationUnits = $this->readSource($sourceFile);
        if (is_null($translationUnits)) {
            return null;
        }

        $ids = [];
        foreach ($translationUnits as $unit) {
            $langString = new I18NLangString();
            $id = (string)$unit['id'];
            $source = (string)$unit->source;
            $langString->setUnitId($id);
            $langString->setValue($source);
            $langString->setSourceId($sourceId);
            // TODO:: group XLIFF source
            //$langString->setGroupId()
            //$langString->setVersion()
            $note = (string)$unit->note;
            if (!empty($note)) {
                $langString->setNote($note);
            }
            try {
                $this->getI18NDao()->saveI18NLangString($langString);
            } catch (Doctrine_Validator_Exception $e) {
                // Lang string already exists
            }

            $ids[] = $id;
        }

        return $ids;
    }

    /**
     * @param array $currentSourceIds
     * @param int $sourceId
     * @return int number of deleted records
     * @throws DaoException
     */
    public function deleteRemovedLangStringsFromDatabase(array $currentSourceIds, int $sourceId)
    {
        return $this->getI18NDao()->deleteLangStrings($currentSourceIds, $sourceId);
    }

    /**
     * @param string $langCode
     * @throws DaoException
     * @throws sfException
     */
    public function syncI18NTranslations(string $langCode)
    {
        $language = $this->getI18NDao()->getLanguageByCode($langCode);

        foreach ($this->getI18NSourceDirs() as $sourceDir) {
            $baseSource = $this->getLangPackPath($sourceDir);
            $source = $this->getLangPackPath($sourceDir, $langCode);

            $i18nSource = $this->getI18NDao()->getI18NSource($source);
            if ($i18nSource instanceof I18NSource) {
                $timestamp = filemtime($source);
                if ($i18nSource->getModifiedAt() < date("Y-m-d H:i:s", $timestamp)) {
                    $this->syncI18NSourceTranslations($baseSource, $source, $language);
                    // Update last sync time
                    $i18nSource->setModifiedAt(date("Y-m-d H:i:s"));
                    $this->getI18NDao()->saveI18NSource($i18nSource);
                }
            } else {
                $this->syncI18NSourceTranslations($baseSource, $source, $language);
                // New source
                $newSource = new I18NSource();
                $newSource->setSource($source);
                $newSource->setModifiedAt(date("Y-m-d H:i:s"));
                $this->getI18NDao()->saveI18NSource($newSource);
            }
        }
    }

    /**
     * @param string $baseSource
     * @param string $source
     * @param I18NLanguage $language
     * @throws DaoException
     */
    public function syncI18NSourceTranslations(string $baseSource, string $source, I18NLanguage $language)
    {
        $i18nSource = $this->getI18NDao()->getI18NSource($baseSource);
        if ($i18nSource instanceof I18NSource) {
            $langStrings = $this->getLangStringsBySourceId($i18nSource->getId());
            $this->saveTranslationsFromSource(
                $source,
                $language->getId(),
                $langStrings
            );
        }
    }

    /**
     * @param int $sourceId
     * @return Doctrine_Collection|I18NLangString[]
     * @throws DaoException
     */
    public function getLangStringsBySourceId(int $sourceId)
    {
        return $this->getI18NDao()->getLangStringsBySourceId($sourceId);
    }

    /**
     * @param string $sourceFile
     * @param int $langId
     * @param $langStrings
     * @throws DaoException
     */
    public function saveTranslationsFromSource(string $sourceFile, int $langId, $langStrings)
    {
        $translationUnits = $this->getTranslationUnitsAssoc(
            $this->readSource($sourceFile)
        );

        foreach ($langStrings as $langString) {
            $translate = new I18NTranslate();
            $translate->setLangStringId($langString->getId());
            $translate->setLanguageId($langId);

            $source = $langString->getValue();
            if (isset($translationUnits[$source]) && !empty($translationUnits[$source])) {
                $translate->setValue($translationUnits[$source]);
            } else {
                $translate->setTranslated(false);
            }
            $translate->setModifiedAt(date("Y-m-d H:i:s"));
            try {
                $this->getI18NDao()->saveI18NTranslate($translate);
            } catch (Doctrine_Exception $e) {
                // Translation already exists
                $i18nTranslate = $this->getI18NDao()->getI18NTranslate($langString->getId(), $langId);
                // Only update if not translated (avoid override customization)
                if (!$i18nTranslate->getCustomized()) {
                    $i18nTranslate->setValue($translate->getValue());
                    $i18nTranslate->setTranslated(!empty($translate->getValue()));
                    $i18nTranslate->setModifiedAt($translate->getModifiedAt());
                    $this->getI18NDao()->saveI18NTranslate($i18nTranslate);
                }
            }
        }
    }

    /**
     * @param SimpleXMLElement[]|null $translationUnits
     * @return array
     */
    public function getTranslationUnitsAssoc(array $translationUnits = null): array
    {
        $assocTranslationUnits = [];
        if (is_null($translationUnits)) {
            return $assocTranslationUnits;
        }

        foreach ($translationUnits as $unit) {
            $source = (string)$unit->source;
            $target = (string)$unit->target;
            if (!empty($target)) {
                $assocTranslationUnits[$source] = $target;
            }
        }
        return $assocTranslationUnits;
    }

    /**
     * @param string $sourceFile absolute location of XLIFF source
     * @return SimpleXMLElement[]|null
     */
    public function readSource(string $sourceFile)
    {
        libxml_use_internal_errors(true);
        if (!$xml = simplexml_load_file($sourceFile)) {
            return null;
        }
        libxml_use_internal_errors(false);

        return $xml->xpath('//trans-unit');
    }

    /**
     * Return valid OrangeHRM I18N sources
     * @return array
     * @throws sfException
     */
    public function getI18NSourceDirs(): array
    {
        $sources = sfContext::getInstance()->getConfiguration()->getI18NGlobalDirs();
        $validSources = [];
        foreach ($sources as $source) {
            if (is_file($this->getLangPackPath($source))) {
                $validSources[] = $source;
            }
        }
        return $validSources;
    }

    /**
     * Return path to messages.zz_ZZ.xml by given source location
     * @param string $source
     * @param string $langCode
     * @return string
     */
    public function getLangPackPath(string $source, string $langCode = self::DEV_LANG_PACK)
    {
        return $source . DIRECTORY_SEPARATOR . sprintf('messages.%s.xml', $langCode);
    }

    public function markLanguageAsModified(string $langCode)
    {
        $lang = $this->getI18NDao()->getLanguageByCode($langCode);
        $lang->setModifiedAt(date("Y-m-d H:i:s"));
        $this->getI18NDao()->saveI18NLanguage($lang);
    }
}

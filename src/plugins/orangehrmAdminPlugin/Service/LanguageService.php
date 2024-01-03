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

use OrangeHRM\Admin\Dao\LanguageDao;
use OrangeHRM\Admin\Dto\LanguageSearchFilterParams;
use OrangeHRM\Entity\Language;

class LanguageService
{
    /**
     * @var LanguageDao|null
     */
    private ?LanguageDao $languageDao = null;

    /**
     * @return LanguageDao
     */
    public function getLanguageDao(): LanguageDao
    {
        if (!($this->languageDao instanceof LanguageDao)) {
            $this->languageDao = new LanguageDao();
        }

        return $this->languageDao;
    }

    /**
     * @param LanguageDao $languageDao
     * @return Void
     */
    public function setLanguageDao(LanguageDao $languageDao): void
    {
        $this->languageDao = $languageDao;
    }

    /**
     * Saves a language
     *
     * Can be used for a new record or updating.
     * @param Language $language
     * @return Language
     */
    public function saveLanguage(Language $language): Language
    {
        return $this->getLanguageDao()->saveLanguage($language);
    }

    /**
     * Retrieves a language by ID
     *
     * @param int $id
     * @return Language An instance of Language or NULL
     */
    public function getLanguageById(int $id): ?Language
    {
        return $this->getLanguageDao()->getLanguageById($id);
    }

    /**
     * Retrieves a language by name
     *
     * Case-insensitive
     *
     * @param string $name
     * @return Language An instance of Language or false
     */
    public function getLanguageByName(string $name): ?Language
    {
        return $this->getLanguageDao()->getLanguageByName($name);
    }

    /**
     * @param LanguageSearchFilterParams $languageSearchParamHolder
     * @return int
     */
    public function getLanguageCount(LanguageSearchFilterParams $languageSearchParamHolder): int
    {
        return $this->getLanguageDao()->getLanguageCount($languageSearchParamHolder);
    }

    /**
     * Retrieves all languages ordered by name
     * @param LanguageSearchFilterParams $languageSearchParamHolder
     * @return array
     */
    public function getLanguageList(LanguageSearchFilterParams $languageSearchParamHolder): array
    {
        return $this->getLanguageDao()->getLanguageList($languageSearchParamHolder);
    }

    /**
     * Deletes languages
     * @param array $toDeleteIds An array of IDs to be deleted
     * @return int Number of records deleted
     */
    public function deleteLanguages(array $toDeleteIds): int
    {
        return $this->getLanguageDao()->deleteLanguages($toDeleteIds);
    }

    /**
     * Checks whether the given language name exists
     *
     * Case-insensitive
     *
     * @param string $languageName Language name that needs to be checked
     * @return bool
     */
    public function isExistingLanguageName(string $languageName): bool
    {
        return $this->getLanguageDao()->isExistingLanguageName($languageName);
    }
}

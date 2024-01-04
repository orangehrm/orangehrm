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

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\I18NLangString;
use OrangeHRM\Entity\I18NLanguage;
use OrangeHRM\Entity\I18NTranslation;

class I18NTranslationDecorator
{
    use EntityManagerHelperTrait;

    private I18NTranslation $i18NTranslation;

    /**
     * @param I18NTranslation $i18NTranslation
     */
    public function __construct(I18NTranslation $i18NTranslation)
    {
        $this->i18NTranslation = $i18NTranslation;
    }

    /**
     * @return I18NTranslation
     */
    protected function getI18NTranslation(): I18NTranslation
    {
        return $this->i18NTranslation;
    }

    /**
     * @param int $langStringId
     * @return void
     */
    public function setLangStringById(int $langStringId): void
    {
        $langString = $this->getReference(I18NLangString::class, $langStringId);
        $this->getI18NTranslation()->setLangString($langString);
    }

    /**
     * @param int $languageId
     * @return void
     */
    public function setLanguageById(int $languageId): void
    {
        $language = $this->getReference(I18NLanguage::class, $languageId);
        $this->getI18NTranslation()->setLanguage($language);
    }
}

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

use OrangeHRM\Admin\Dao\I18NDao;
use OrangeHRM\Admin\Dto\I18NLanguageSearchFilterParams;
use OrangeHRM\Admin\Service\Model\I18NLanguageModel;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use Exception;

class I18NService
{
    use NormalizerServiceTrait;

    /**
     * @var I18NDao|null
     */
    private ?I18NDao $i18NDao = null;

    /**
     * @return I18NDao|null
     */
    public function getI18NDao(): I18NDao
    {
        if (!($this->i18NDao instanceof I18NDao)) {
            $this->i18NDao = new I18NDao();
        }

        return $this->i18NDao;
    }

    /**
     * @param I18NDao $i18NDao
     */
    public function setI18NDao(I18NDao $i18NDao): void
    {
        $this->i18NDao = $i18NDao;
    }

    /**
     * @param I18NLanguageSearchFilterParams $i18NLanguageSearchParams
     * @return array
     */
    public function searchLanguages(I18NLanguageSearchFilterParams $i18NLanguageSearchParams): array
    {
        return $this->getI18NDao()->searchLanguages($i18NLanguageSearchParams);
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
}

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

use OrangeHRM\Admin\Dto\I18NLanguageSearchFilterParams;
use OrangeHRM\Core\Traits\Service\I18NServiceTrait;

class LocalizationService
{
    use I18NServiceTrait;

    /**
     * @return string[]
     */
    public function getLocalizationDateFormats(): array
    {
        return [
            ['id' => 'Y-m-d', 'label' => 'yyyy-mm-dd ( ' . date('Y-m-d') . ' )'],
            ['id' => 'd-m-Y', 'label' => 'dd-mm-yyyy ( ' . date('d-m-Y') . ' )'],
            ['id' => 'm-d-Y', 'label' => 'mm-dd-yyyy ( ' . date('m-d-Y') . ' )'],
            ['id' => 'Y-d-m', 'label' => 'yyyy-dd-mm ( ' . date('Y-d-m') . ' )'],
            ['id' => 'm-Y-d', 'label' => 'mm-yyyy-dd ( ' . date('m-Y-d') . ' )'],
            ['id' => 'd-Y-m', 'label' => 'dd-yyyy-mm ( ' . date('d-Y-m') . ' )'],
            ['id' => 'Y/m/d', 'label' => 'yyyy/mm/dd ( ' . date('Y/m/d') . ' )'],
            ['id' => 'Y m d', 'label' => 'yyyy mm dd ( ' . date('Y m d') . ' )'],
            ['id' => 'Y-M-d', 'label' => 'yyyy-M-dd ( ' . date('Y-M-d') . ' )'],
            ['id' => 'l, d-M-Y', 'label' => 'DD, dd-M-yyyy ( ' . date('l, d-M-Y') . ' )'],
            ['id' => 'D, d M Y', 'label' => 'D, dd M yyyy ( ' . date('D, d M Y') . ' )']
        ];
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getSupportedLanguages(): array
    {
        $i18NLanguageSearchParams = new I18NLanguageSearchFilterParams();
        $i18NLanguageSearchParams->setAddedOnly(true);
        $i18NLanguageSearchParams->setEnabledOnly(true);

        return $this->getI18NService()->getLanguagesArray($i18NLanguageSearchParams);
    }
}

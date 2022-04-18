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

namespace OrangeHRM\I18N\Service;

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Services;

class I18NHelper
{
    use ServiceContainerTrait;

    /**
     * @return I18NService
     */
    private function getI18NService(): I18NService
    {
        return $this->getContainer()->get(Services::I18N_SERVICE);
    }

    /**
     * @param string $key
     * @param array $parameters
     * @param string|null $langCode
     * @return string
     */
    public function trans(string $key, array $parameters = [], string $langCode = null): string
    {
        if (!Config::get(Config::I18N_ENABLED)) {
            return $key;
        }
        return $this->getI18NService()->trans($key, $parameters, $langCode);
    }

    /**
     * @param string $sourceLangString
     * @param array $parameters
     * @param string|null $langCode
     * @return string
     */
    public function transBySource(string $sourceLangString, array $parameters = [], string $langCode = null): string
    {
        if (!Config::get(Config::I18N_ENABLED)) {
            return $sourceLangString;
        }
        return $this->getI18NService()->transBySource($sourceLangString, $parameters, $langCode);
    }

    /**
     * @param string $langCode
     */
    public function setTranslatorLanguage(string $langCode): void
    {
        $this->getI18NService()->setTranslatorLanguage($langCode);
    }
}

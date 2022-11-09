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


class HelpService
{

    const HELP_MODULE_NAME = 'help';

    protected $helpConfigService;
    public $helpProcessorClass;
    public $navigationLogService;
    public $moduleService;

    /**
     * @return HelpConfigService
     */
    public function getHelpConfigService()
    {
        if (!$this->helpConfigService instanceof HelpConfigService) {
            $this->helpConfigService = new HelpConfigService();
        }
        return $this->helpConfigService;
    }

    /**
     * @param HelpConfigService $helpConfigService
     */
    public function setHelpConfigService(HelpConfigService $helpConfigService)
    {
        $this->helpConfigService = $helpConfigService;
    }

    /**
     * @return mixed
     */
    public function getHelpProcessorClass()
    {
        if (!$this->helpProcessorClass instanceof HelpProcessor) {
            $helpProcessorClassName = $this->getHelpConfigService()->getHelpProcessorClass();
            $this->helpProcessorClass = new $helpProcessorClassName();
        }
        return $this->helpProcessorClass;
    }

    /**
     * @param mixed $helpProcessorClass
     */
    public function setHelpProcessorClass(HelpProcessor $helpProcessorClass)
    {
        $this->helpProcessorClass = $helpProcessorClass;
    }

    /**
     * @param $label
     * @return mixed
     */
    public function getRedirectUrl($label)
    {
        if (!$this->helpProcessorClass instanceof HelpProcessor) {
            $this->helpProcessorClass = $this->getHelpProcessorClass();
        }
        return $this->helpProcessorClass->getRedirectUrl($label);
    }

    public function getDefaultRedirectUrl()
    {
        if (!$this->helpProcessorClass instanceof HelpProcessor) {
            $this->helpProcessorClass = $this->getHelpProcessorClass();
        }

        return $this->helpProcessorClass->getDefaultRedirectUrl();
    }

    public function getBaseUrl()
    {
        if (!$this->helpProcessorClass instanceof HelpProcessor) {
            $this->helpProcessorClass = $this->getHelpProcessorClass();
        }

        return $this->helpProcessorClass->getBaseUrl();
    }

    public function getSearchUrl($label)
    {
        if (!$this->helpProcessorClass instanceof HelpProcessor) {
            $this->helpProcessorClass = $this->getHelpProcessorClass();
        }

        return $this->helpProcessorClass->getSearchUrl($label);
    }

    public function getRedirectUrlList($query, $labels = [], $categoryIds = [])
    {
        if (!$this->helpProcessorClass instanceof HelpProcessor) {
            $this->helpProcessorClass = $this->getHelpProcessorClass();
        }
        return $this->helpProcessorClass->getRedirectUrlList($query, $labels, $categoryIds);
    }

    public function getCategoryRedirectUrl($category)
    {
        if (!$this->helpProcessorClass instanceof HelpProcessor) {
            $this->helpProcessorClass = $this->getHelpProcessorClass();
        }
        return $this->helpProcessorClass->getCategoryRedirectUrl($category);
    }

    public function getCategoriesFromSearchQuery($query = null)
    {
        if (!$this->helpProcessorClass instanceof HelpProcessor) {
            $this->helpProcessorClass = $this->getHelpProcessorClass();
        }
        return $this->helpProcessorClass->getCategoriesFromSearchQuery($query);
    }

    /**
     * @return bool
     */
    public function isValidUrl()
    {
        $validUrl = false;
        if (filter_var($this->getHelpConfigService()->getBaseHelpUrl(), FILTER_VALIDATE_URL)) {
            $validUrl = true;
        }
        return $validUrl;
    }

}

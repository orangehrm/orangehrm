<?php

/*
  // OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
  // all the essential functionalities required for any enterprise.
  // Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com

  // OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
  // the GNU General Public License as published by the Free Software Foundation; either
  // version 2 of the License, or (at your option) any later version.

  // OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
  // without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
  // See the GNU General Public License for more details.

  // You should have received a copy of the GNU General Public License along with this program;
  // if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
  // Boston, MA  02110-1301, USA
 */

/**
 * Gives access to OrangeHRM config files sysConf.php and conf.php
 */
class OrangeConfig {

    private $sysConf = null;
    private $conf = null;
    private $appConf = null;
    private $configService = null;
    private static $instance = null;    

    /**
     * Private constructor. Use the getInstance() method to get object instance
     */
    private function __construct() {
        
    }

    /**
     * Returns an instance of this class
     *
     * @return OrangeConfig
     */
    public static function getInstance() {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get orangehrm's sysConf configuration object
     *
     * @return sysConf object
     */
    public function getSysConf() {
        if (is_null($this->sysConf)) {

            require_once sfConfig::get('sf_root_dir') . '/../lib/confs/sysConf.php';
            $this->sysConf = new sysConf();
        }

        return $this->sysConf;
    }

    /**
     * Get orangehrm's Conf configuration object
     *
     * @return Conf object
     */
    public function getConf() {
        if (is_null($this->conf)) {

            require_once sfConfig::get('sf_root_dir') . '/../lib/confs/Conf.php';
            $this->conf = new Conf();
        }

        return $this->conf;
    }

    public function getAppConfValue($key) {

        $configService = $this->getConfigService();
        switch ($key) {
            case ConfigService :: KEY_LEAVE_PERIOD_DEFINED:
                return $configService->isLeavePeriodDefined();
                break;

            case ConfigService::KEY_PIM_SHOW_DEPRECATED:
                return $configService->showPimDeprecatedFields();
                break;
            case ConfigService::KEY_PIM_SHOW_SSN:
                return $configService->showPimSSN();
                break;
            case ConfigService::KEY_PIM_SHOW_SIN:
                return $configService->showPimSIN();
                break;
            case ConfigService::KEY_PIM_SHOW_TAX_EXEMPTIONS:
                return $configService->showPimTaxExemptions();
                break;
            case ConfigService::KEY_NON_LEAP_YEAR_LEAVE_PERIOD_START_DATE:
                return $configService->getNonLeapYearLeavePeriodStartDate();
                break;                
            case ConfigService::KEY_IS_LEAVE_PERIOD_START_ON_FEB_29:
                return $configService->getIsLeavePeriodStartOnFeb29th();
                break;
            case ConfigService::KEY_LEAVE_PERIOD_START_DATE:
                return $configService->getLeavePeriodStartDate();
                break;
            case ConfigService::KEY_THEME_NAME:
                return $configService->getThemeName();
                break;  
            case ConfigService::KEY_LEAVE_PERIOD_STATUS:
                return $configService->getLeavePeriodStatus();
                break; 
            default:
                throw new Exception("Getting {$key} is not implemented yet");
                break;
        }
    }

    public function setAppConfValue($key, $value) {

        $configService = $this->getConfigService();

        switch ($key) {
            case ConfigService:: KEY_LEAVE_PERIOD_DEFINED:
                $configService->setIsLeavePeriodDefined($value);
                break;

            case ConfigService::KEY_PIM_SHOW_DEPRECATED:
                $configService->setShowPimDeprecatedFields($value);
                break;
            case ConfigService::KEY_PIM_SHOW_SSN:
                return $configService->setShowPimSSN($value);
                break;
            case ConfigService::KEY_PIM_SHOW_SIN:
                return $configService->setShowPimSIN($value);
                break;
            case ConfigService::KEY_PIM_SHOW_TAX_EXEMPTIONS:
                return $configService->setShowPimTaxExemptions($value);
                break;            
            case ConfigService::KEY_NON_LEAP_YEAR_LEAVE_PERIOD_START_DATE:
                return $configService->setNonLeapYearLeavePeriodStartDate($value);
                break;                
            case ConfigService::KEY_IS_LEAVE_PERIOD_START_ON_FEB_29:
                return $configService->setIsLeavePeriodStartOnFeb29th($value);
                break;
            case ConfigService::KEY_LEAVE_PERIOD_START_DATE:
                return $configService->setLeavePeriodStartDate($value);
                break;
            case ConfigService::KEY_THEME_NAME:
                return $configService->setThemeName($value);
                break;  
            case ConfigService::KEY_LEAVE_PERIOD_STATUS:
                 return $configService->setLeavePeriodStatus($value);
                break;
            default:
                throw new Exception("Setting {$key} is not implemented yet");
                break;
        }
    }
    
    protected function getConfigService() {
        if (!isset($this->configService)) {
            $this->configService = new ConfigService();
        }
        
        return $this->configService;
    }

}
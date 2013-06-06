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
class EmailConfigurationService extends BaseService {

    private $emailConfigurationDao;

    /**
     * Construct
     */
    public function __construct() {
        $this->emailConfigurationDao = new EmailConfigurationDao();
    }

    /**
     * @ignore
     */
    public function getEmailConfigurationDao() {
        return $this->emailConfigurationDao;
    }

    /**
     * @ignore
     */
    public function setEmailConfigurationDao(EmailConfigurationDao $emailConfigurationDao) {
        $this->emailConfigurationDao = $emailConfigurationDao;
    }

    /**
     * Retrieve EmailConfiguration
     * 
     * Fetch the existing email configuration or create a new one if not exists
     * 
     * @version 2.7 
     * @return Doctrine Collection 
     */
    public function getEmailConfiguration() {
        $emailConfiguration = $this->emailConfigurationDao->getEmailConfiguration();
        
        if (!$emailConfiguration) {
            $emailConfiguration = new EmailConfiguration();
            $emailConfiguration->setId(1);
            return $emailConfiguration;
        } else {
            return $emailConfiguration;
        }
    }
    
    /**
     * Save EmailConfiguration
     * 
     * Can be used for a new record or updating.
     * 
     * @version 2.7
     * @param EmailConfiguration $emailConfiguration
     * @return NULL Doesn't return a value
     */
    public function saveEmailConfiguration(EmailConfiguration $emailConfiguration) {
        $this->emailConfigurationDao->saveEmailConfiguration($emailConfiguration);
    }


}

?>

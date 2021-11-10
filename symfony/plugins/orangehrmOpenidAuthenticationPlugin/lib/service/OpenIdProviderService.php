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

/**
 * Description of OpenIdProviderService
 *
 * @author orangehrm
 */
class OpenIdProviderService extends BaseOpenIdService {
     private $openIdProviderDao;
    /**
     *
     * @param OpenIdProviderDao $dao 
     */
    public function setOpenIdProviderDao($dao) {
        $this->openIdProviderDao = $dao;
    }

    /**
     *
     * @return OpenIdProviderDao 
     */
    public function getOpenIdProviderDao() {
        if (!isset($this->openIdProviderDao)) {
            $this->openIdProviderDao = new OpenIdProviderDao();
        }
        return $this->openIdProviderDao;
    }
     /**
     *
     * @param OpenidProvider $openIdProvider
     * @return OpenidProvider 
     */
    public function saveOpenIdProvider(OpenidProvider $openIdProvider){
          return $this->getOpenIdProviderDao()->saveOpenIdProvider($openIdProvider);
    }
    /**
     *
     * @param bool $isActive 
     * @return OpenidProvider 
     */
    public function listOpenIdProviders($isActive =true){
       return $this->getOpenIdProviderDao()->listOpenIdProviders($isActive =true);

    }
    /**
     *
     * @param int $id 
     * @return mix 
     */
    public function removeOpenIdProvider($id){
       return $this->getOpenIdProviderDao()->removeOpenIdProvider($id);
        
    }
    /**
     * Get Open Id Provider by ID
     * @return OpenidProvider
     */
    public function getOpenIdProvider($id) {
       return $this->getOpenIdProviderDao()->getOpenIdProvider($id);
    }   
}

?>

<?php

/*
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM) 
 * System that captures all the essential functionalities required for any enterprise. 
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com 
 * 
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any 
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc 
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the 
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain 
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property 
 * rights to any design, new software, new protocol, new interface, enhancement, update, 
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for 
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are 
 * reserved to OrangeHRM Inc. 
 * 
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms and conditions on using this software. 
 *  
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

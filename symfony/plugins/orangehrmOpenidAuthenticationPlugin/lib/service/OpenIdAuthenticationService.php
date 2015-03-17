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
 * Description of OpenIdAuthenticationService
 *
 * @author orangehrm
 */
class OpenIdAuthenticationService extends AuthenticationService {
    private $openIdAuthDao;
    private $openIdentityDao;
    /**
     *
     * @param OpenIdAuthenticationDao $dao 
     */
    public function setOpenIdAuthenticationDao($dao) {
        $this->openIdAuthDao = $dao;
    }

    /**
     *
     * @return OpenIdAuthenticationDao 
     */
    public function getOpenIdAuthenticationDao() {
        if (!isset($this->openIdAuthDao)) {
            $this->openIdAuthDao = new OpenIdAuthenticationDao();
        }
        return $this->openIdAuthDao;
    }
    /**
     *
     * @return OpenIdentityDao 
     */
    public function getOpenIdentityDao(){
        if ($this->openIdentityDao == null) {
            $this->openIdentityDao = new OpenIdentityDao();
        }
        return $this->openIdentityDao;
        
    }
    /**
     *
     * @param OpenIdentityDao $dao 
     */
    public function setOpenIdentityDao($dao){
      $this->openIdentityDao = $dao;
    }
     /**
     *
     * @param string $username
     * @param array $additionalData
     * @return bool
     * @throws AuthenticationServiceException
     */
    public function setOpenIdCredentials($username, $additionalData) {
        $user = $this->getOpenIdAuthenticationDao()->getOpenIdCredentials($username);

        if (is_null($user) || !$user) {
            return false;
        } else {
            sfContext::getInstance()->getConfiguration()->loadHelpers('Url');

            if ($user->getIsAdmin() == 'No' && $user->getEmpNumber() == '') {
                throw new AuthenticationServiceException('Employee not assigned');
            } elseif (!is_null($user->getEmployee()->getTerminationId())) {
                throw new AuthenticationServiceException('Employee is terminated');
            } elseif ($user->getStatus() == 0) {
                throw new AuthenticationServiceException('Account disabled');
            }

            $identity=$this->getOpenIdentityDao()->getOpenIdentity($user->getId(), $additionalData['providerid']);
            
           if (!(count($identity)) > 0) {
                $identity =new UserIdentitiy();
                $identity->setUserId($user->getId());
                $identity->setProviderId($additionalData['providerid']);
                $identity->setUserIdentity($additionalData['useridentity']);
                $identity->save();
                
            }
            if($identity instanceof UserIdentitiy ){
                if($identity->getUserIdentity()!=$additionalData['useridentity']){
                    return false;
                }
            }
            
            session_regenerate_id(TRUE);
            
            $this->setBasicUserAttributes($user);
            $this->setBasicUserAttributesToSession($user);
            $this->setRoleBasedUserAttributes($user);
            $this->setRoleBasedUserAttributesToSession($user);
            $this->setSystemBasedUserAttributes($user, $additionalData);
            $this->setSystemBasedUserAttributesToSession($user, $additionalData);

            $webRoot = sfContext::getInstance()->getRequest()->getRelativeUrlRoot();
            $this->getCookieManager()->setCookie('Loggedin', 'True', 0, $webRoot);
            return true;
        }
        return true;
    }
}

?>

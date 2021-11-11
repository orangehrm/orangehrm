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
        if (is_array($additionalData['useridentity'])) {
            $additionalData['useridentity'] = json_encode($additionalData['useridentity']);
        }
        $user = $this->getOpenIdAuthenticationDao()->getOpenIdCredentials($username);

        if (is_null($user) || !$user) {
            return false;
        } else {
            sfContext::getInstance()->getConfiguration()->loadHelpers('Url');

            if ($user->getIsAdmin() == 'No' && $user->getEmpNumber() == '') {
                throw new AuthenticationServiceException(__('Employee not assigned'));
            } elseif (!is_null($user->getEmployee()->getTerminationId())) {
                throw new AuthenticationServiceException(__('Employee is terminated'));
            } elseif ($user->getStatus() == 0) {
                throw new AuthenticationServiceException(__('Account disabled'));
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

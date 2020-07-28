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


class OAuthClientDao extends BaseOpenIdDao{
    const PUBLIC_MOBILE_CLIENT_ID = 'orangehrm_mobile_app';

    /**
     * List OAuth Clients
     *
     * @return Doctrine_Collection
     * @throws DaoException
     */
    public function listOAuthClients(){
        try {
           $query = Doctrine_Query::create()
                    ->from('OAuthClient');

            return $query->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }

    }

    /**
     * Delete OAuth client
     *
     * @param $id
     * @return Doctrine_Collection
     * @throws DaoException
     */
    public function deleteOAuthClient($id){
        try {
          $query = Doctrine_Query::create()
                ->delete('OAuthClient')
                ->whereIn('client_id',$id);

          return $query->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }

    }

    /**
     * Get OAuth client by ID
     *
     * @param $id
     * @return mixed
     * @throws DaoException
     */
    public function getOAuthClient($id) {
        try {
            return Doctrine::getTable('OAuthClient')->find($id);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Create mobile OAuth client with default settings
     * @throws DaoException
     */
    public function createMobileClient()
    {
        try {
            $client = new OAuthClient();
            $client->setClientId(self::PUBLIC_MOBILE_CLIENT_ID);
            $client->setClientSecret('');
            $client->setRedirectUri('');
            $client->setGrantTypes(sprintf("%s %s", GrantType::USER_CREDENTIALS, GrantType::REFRESH_TOKEN));
            $client->setScope(Scope::SCOPE_USER);
            $client->save();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
}

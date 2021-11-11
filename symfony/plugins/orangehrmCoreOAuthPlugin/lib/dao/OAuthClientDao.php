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

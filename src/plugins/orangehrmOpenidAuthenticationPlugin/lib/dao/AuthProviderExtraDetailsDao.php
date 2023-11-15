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
 * Description of AuthProviderExtraDetailsDao
 */
class AuthProviderExtraDetailsDao {

    /**
     * Get Authentication Provider Extra Details by Provider Id
     * 
     * @param type $providerId
     * @return AuthProviderExtraDetails
     * @throws DaoException
     */
    public function getAuthProviderDetailsByProviderId($providerId) {
        try {
            $query = Doctrine_Query::create()
                    ->from('AuthProviderExtraDetails')
                    ->where('provider_id =?', $providerId);
            return $query->fetchOne();
            //@codeCoverageIgnoreStart
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage(), $ex->getCode(), $ex);
        }
        //@codeCoverageIgnoreEnd 
    }

    /**
     * Save Authentication Provider Extra Details
     * 
     * @param AuthProviderExtraDetails $authProvider
     * @return AuthProviderExtraDetails
     * @throws DaoException
     */
    public function saveAuthProviderExtraDetails(AuthProviderExtraDetails $authProvider) {
        try {
            $authProvider->save();
            return $authProvider;
            // @codeCoverageIgnoreStart
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage(), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }

}

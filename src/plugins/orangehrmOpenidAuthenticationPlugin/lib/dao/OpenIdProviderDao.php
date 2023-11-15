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
 * Description of OpenIdProviderDao
 *
 * @author orangehrm
 */
class OpenIdProviderDao extends BaseOpenIdDao{
    /**
     *
     * @param OpenidProvider $openIdProvider
     * @return OpenidProvider
     */
    public function saveOpenIdProvider(OpenidProvider $openIdProvider){
            $openIdProvider->save();
            $openIdProvider->refresh();
            return $openIdProvider;

    }
    /**
     *
     * @param bool $isActive
     * @return OpenidProvider
     */
    public function listOpenIdProviders($isActive =true){
           $query = Doctrine_Query::create()
                    ->from('OpenidProvider');
            if($isActive){
                $query->andWhere('status = ?',1);
            }
            return $query->execute();

    }
    /**
     *
     * @param int $id
     * @return mix
     */
    public function removeOpenIdProvider($id){
          $query = Doctrine_Query::create()
                ->update('OpenidProvider')
                ->set('status','?',0)
                ->whereIn('id',$id);

          return $query->execute();

    }
    /**
     * Get Open Id Provider by ID
     * @return OpenidProvider
     */
    public function getOpenIdProvider($id) {
        return Doctrine::getTable('OpenidProvider')->find($id);
    }
}

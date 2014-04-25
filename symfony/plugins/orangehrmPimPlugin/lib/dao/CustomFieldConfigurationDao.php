<?php

/*
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

class CustomFieldConfigurationDao extends BaseDao {

    /**
     * Retrieve Custom Fields
     * @param String $orderField
     * @param String $orderBy
     * @returns Collection
     * @throws DaoException
     */
    public function getCustomFieldList($screen = null, $orderField = "name", $orderBy = "ASC") {
        
        try {
            
            $q = Doctrine_Query::create()
                            ->from('CustomField');

            if (!empty($screen)) {
                $q->where('screen = ?', $screen);
            }

            // Only allow DESC or ASC for security
            $orderBy = strcasecmp($orderBy, 'DESC') === 0 ? 'DESC' : 'ASC';
            $q->orderBy($orderField . ' ' . $orderBy);

            return $q->execute();
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
        
    }

    /**
     * Save CustomField
     * @param CustomField $customField
     * @returns CustomField
     * @throws DaoException, DataDuplicationException
     */
    public function saveCustomField(CustomField $customField) {
        
        try {
            
            $q = Doctrine_Query::create()
                            ->from('CustomField c')
                            ->where('c.name = ?', $customField->name)
                            ->andWhere('c.id <> ?', $customField->id);

            $freeNum = null;

            if (empty($customField->id)) {
                
                $q = Doctrine_Query::create()
                                ->select('c.field_num')
                                ->from('CustomField c')
                                ->orderBy('id');
                
                $fieldNumbers = $q->execute(array(), Doctrine::HYDRATE_SCALAR);
                $count = count($fieldNumbers);

                $i = 1;
                foreach ($fieldNumbers as $num) {

                    if ($num['c_id'] > $i) {
                        $freeNum = $i;
                        break;
                    }
                    $i++;

                    if ($i > 10) {
                        break;
                    }
                    
                }

                if (empty($freeNum) && ($i <= 10)) {
                    $freeNum = $i;
                }

                $customField->id = $freeNum;
                
            }

            if (!empty($customField->id)) {
                $customField->save();
            }

            return $customField;

        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
        
    }

    /**
     * Delete CustomField
     * @param array() $customFieldList
     * @returns integer
     * @throws DaoException
     */
    public function deleteCustomFields($customFieldIdList) {
        
        try {
            
            if (!is_array($customFieldIdList) || empty($customFieldIdList)) {
                throw new DaoException('Invalid parameter: $customFieldList should be an array and should not be empty');
            }            
            
            $this->_deleteReletedEmployeeCustomFields($customFieldIdList);

            $q = Doctrine_Query::create()
                            ->delete('CustomField')
                            ->whereIn('id', $customFieldIdList);

            return $q->execute();
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
        
    }

    private function _deleteReletedEmployeeCustomFields($customFieldIdList) {

        try {
            
            $rows = 0;
            
            foreach ($customFieldIdList as $id) {
                
                $actualFieldName = "custom" . $id;

                $q = Doctrine_Query::create()
                                ->update('Employee')
                                ->set($actualFieldName, '?', '');

                $rows += $q->execute();
                
            }
            
            return $rows;
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Returns CustomField by Id
     * @param int $id
     * @returns CustomField
     * @throws DaoException
     */
    public function getCustomField($id) {
        
        try {
            
            $result = Doctrine::getTable('CustomField')->find($id);
            
            if (!$result) {
                return null;
            }
            
            return $result;
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
        
    }

    
    
}

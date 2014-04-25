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
class LocationDao extends BaseDao {

	/**
	 *
	 * @param type $locationId
	 * @return type 
	 */
	public function getLocationById($locationId) {

		try {
			return Doctrine :: getTable('Location')->find($locationId);
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	/**
	 *
	 * @param type $srchClues
	 * @return type 
	 */
	public function getSearchLocationListCount($srchClues) {

		try {
			$q = $this->_buildSearchQuery($srchClues);
			return $q->count();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	/**
	 *
	 * @param type $srchClues
	 * @return type 
	 */
	public function searchLocations($srchClues) {

                if (!isset($srchClues['sortField'])) {
                    $srchClues['sortField'] = 'name';
                }
                
                if (!isset($srchClues['sortOrder'])) {
                    $srchClues['sortOrder'] = 'ASC';
                }
                
                if (!isset($srchClues['offset'])) {
                    $srchClues['offset'] = 0;
                }
                
                if (!isset($srchClues['limit'])) {
                    $srchClues['limit'] = 50;
                }
                
		$sortField = $this->_getSortField($srchClues['sortField']);
                $sortOrder = strcasecmp($srchClues['sortOrder'], 'DESC') === 0 ? 'DESC' : 'ASC';
		$offset = ($srchClues['offset'] == "") ? 0 : $srchClues['offset'];
		$limit = ($srchClues['limit'] == "") ? 50 : $srchClues['limit'];

		try {
			$q = $this->_buildSearchQuery($srchClues);            
			$q->orderBy($sortField . ' ' . $sortOrder)
				->offset($offset)
				->limit($limit);                        
			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	/**
	 *
	 * @param type $srchClues
	 * @return type 
	 */
	private function _buildSearchQuery($srchClues) {

		$q = Doctrine_Query::create()
                ->select('l.* , IFNULL( Count(el.emp_number),0 ) as numberOfEmployees')
                ->from('Location l')
                ->leftJoin('l.country c')
                ->leftJoin('l.EmpLocations el');

        if (!empty($srchClues['name'])) {
            $q->addWhere('l.name LIKE ?', "%" . trim($srchClues['name']) . "%");
        }
        if (!empty($srchClues['city'])) {
            $q->addWhere('l.city LIKE ?', "%" . trim($srchClues['city']) . "%");
        }
        if (!empty($srchClues['country'])) {
            if (is_array($srchClues['country'])) {
                $q->andWhereIn('l.country_code', $srchClues['country']);
            } else {
                $q->addWhere('l.country_code = ?', $srchClues['country']);
            }
        }
        $q->groupBy('l.id');
        return $q;
	}

	/**
	 *
	 * @param type $locationId
	 * @return type 
	 */
	public function getNumberOfEmplyeesForLocation($locationId) {

		try {
			$q = Doctrine_Query :: create()
				->from('EmpLocations')
				->where('location_id = ?', $locationId);
			return $q->count();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	/**
	 *
	 * @return type 
	 */
	public function getLocationList() {
		
		try {
			$q = Doctrine_Query :: create()
				->from('Location l')
                                ->orderBy('l.name ASC');
			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}
        
    /**
     * Get LocationIds for Employees with the given employee numbers
     * 
     * @param Array $empNumbers Array of employee numbers
     * @return Array of locationIds of the given employees
     */
    public function getLocationIdsForEmployees($empNumbers) {
        try {
            $locationIds = array();
            
            if (count($empNumbers) > 0) {
                $q = Doctrine_Query :: create()
                        ->select('DISTINCT l.locationId')
                        ->from('EmpLocations l')
                        ->whereIn('l.empNumber', $empNumbers);
                $locationIds = $q->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
                
                if (is_string($locationIds)) {
                    $locationIds = array($locationIds);
                }                
            }
            
            return $locationIds;
            
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    
    /**
     * Returns corresponding sort field
     * 
     * @version 2.7.1
     * @param string $sortFieldName 
     * @return string 
     */
    private function _getSortField($sortFieldName){
        
        $sortField = 'l.name';
        if($sortFieldName === 'name') {
            $sortField = 'l.name';
        } else if ($sortFieldName === 'countryName') {
            $sortField = 'c.name';        
        } else if ($sortFieldName === 'city') {
            $sortField = 'l.city';
        } else if ($sortFieldName === 'numberOfEmployees') {
            $sortField = 'numberOfEmployees';
        } 
        
        return $sortField;
        
    }
}



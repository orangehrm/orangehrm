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
 * Description of getEmployeeListAjaxAction
 *
 * @author samantha
 */
class getEmployeeListAjaxAction  extends sfAction{
    
    	/**
	 * get Red hat location by country
	 * 
	 */
	public function execute($request){

        $jsonArray = array();
        
        $requiredPermissions = $request->getParameter('required_permissions', array());
        if (!empty($requiredPermissions)) {
            $requiredPermissions = json_decode($requiredPermissions);
        }

        $properties = array("empNumber","firstName", "middleName", "lastName", "termination_id");
        $employeeNameList = UserRoleManagerFactory::getUserRoleManager()->getAccessibleEntityProperties('Employee', 
                $properties, null, null, array(), array(), $requiredPermissions);
        
        foreach ($employeeNameList as $id => $attributes) {
            $name = trim(trim($attributes['firstName'] . ' ' . $attributes['middleName'],' ') . ' ' . $attributes['lastName']);
            if ($attributes['termination_id']) {
                $name = $name. ' ('.__('Past Employee') .')';
            }
            $jsonArray[$attributes['empNumber']] = array('name' => $name, 'id' => $attributes['empNumber']);
        }
        usort($jsonArray, array($this, 'compareByName'));
        $jsonString = json_encode($jsonArray);

        echo $jsonString;
        exit;

	}
    
    protected function compareByName($employee1, $employee2) {
        return strcmp($employee1['name'], $employee2['name']);
    }
}

?>

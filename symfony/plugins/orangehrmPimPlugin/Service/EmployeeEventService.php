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
 * Employee Event Service
 * 
 *
  * @package pim
 */

class EmployeeEventService extends BaseService {
    
    /**
     * @ignore
     * @var EmployeeEventDao
     */
    private $employeeEventDao;

    /**
     * @return EmployeeEventDao
     */
    public function getEmployeeEventDao() {
        
        if (!($this->employeeEventDao instanceof EmployeeEventDao)) {
            $this->employeeEventDao = new EmployeeEventDao();
        }
        
        return $this->employeeEventDao;
    }

    /**
     * @param $employeeEventDao
     */
    public function setEmployeeEventDao($employeeEventDao) {
        $this->employeeEventDao = $employeeEventDao;
    }
    
    /**
     * Saves a employee event
     * 
     * To use in employee events.
     * 
     * Save employee | Update contact details | Update dependents ...etc
     */
    public function saveEmployeeEvent(EmployeeEvent $employeeEvent) {
        return $this->getEmployeeEventDao()->saveEmployeeEvent($employeeEvent);
    }

    /**
     * Save employee event with parameters
     *
     * @param $empId
     * @param $type
     * @param $event
     * @param $note
     * @param $createdBy
     */
    public function saveEvent($empId, $type, $event, $note, $createdBy)
    {
        $employeeEvent = new EmployeeEvent();
        $employeeEvent->setEmployeeId($empId);
        $employeeEvent->setType($type);
        $employeeEvent->setEvent($event);
        $employeeEvent->setNote($note);
        $employeeEvent->setCreatedBy($createdBy);
        $employeeEvent->setCreatedDate(date("Y-m-d h:i:sa"));
        $this->saveEmployeeEvent($employeeEvent);
    }

    /**
     * Get employee event
     * Get events with parameters
     * fromDate|toDate|empId|event|type
     *
     * @param ParameterObject $parameters
     * @return Doctrine_Collection
     */
    public function getEmployeeEvent(ParameterObject $parameters){
        return $this->getEmployeeEventDao()->getEmployeeEvent($parameters);
    }

    /**
     * Get user role
     *
     * @return string
     */
    public function getUserRole()
    {
        $user = UserRoleManagerFactory::getUserRoleManager()->getUser();
        if ($user instanceof SystemUser) {
            return $user->getUserRole()->getName();
        } else {
            return 'System';
        }
    }

}
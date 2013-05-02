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
 *
 */

/**
 * Description of WorkScheduleService
 */
class WorkScheduleService {
    
    protected $leaveConfigService;
    protected $workScheduleImplementationClass;
    protected $logger;
    
    public function __construct() {
        $this->logger = Logger::getLogger('leave.WorkScheduleService');
    }

    public function getLeaveConfigurationService() {
        if (!($this->leaveConfigService instanceof LeaveConfigurationService)) {
            $this->leaveConfigService = new LeaveConfigurationService();
        }        
        return $this->leaveConfigService;
    }

    public function setLeaveConfigurationService($leaveConfigService) {
        $this->leaveConfigService = $leaveConfigService;
    }   
    
    public function getWorkSchedule($empNumber) {
        
        if (!isset($this->workScheduleImplementationClass)) {            
            $this->workScheduleImplementationClass = $this->getLeaveConfigurationService()->getWorkScheduleImplementation();  
            
            if (empty($this->workScheduleImplementationClass)) {
                $this->logger->error('No work schedule implementation defined');
                throw new ConfigurationException('Work Schedule implemenentation not defined');
            }            
            
            if (!class_exists($this->workScheduleImplementationClass)) {
                throw new ConfigurationException('Work Schedule implemenentation class ' .
                        $this->workScheduleImplementationClass . ' does not exist.');
            }
        }

        try {
            $workSchedule = new $this->workScheduleImplementationClass;                       
        } catch (Exception $e) {
            $this->logger->error('Error constructing work schedule implementation ' . 
                    $this->workScheduleImplementationClass, $e);
            throw new ConfigurationException('Work schedule implementation not configured', 0, $e);
        }
        
        if (!$workSchedule instanceof WorkScheduleInterface) {
            throw new ConfigurationException('Invalid work schedule implemenentation class ' .
                        $this->workScheduleImplementationClass);
        }
        
        $workSchedule->setEmpNumber($empNumber);
        
        return $workSchedule;
    }
}

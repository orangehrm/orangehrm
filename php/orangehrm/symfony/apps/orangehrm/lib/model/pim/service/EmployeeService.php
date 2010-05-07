<?php
/*
 *
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
 * Employee Service
 */
class EmployeeService extends BaseService {


    /**
     * Add a new employee
     *
     * @param CompanyGeninfo $CompanyGeninfo
     * @return Company
     */
    public function addEmployee(Employee $employee) {

        try {
            if( $employee->getEmpNumber() == '') {
	        	$idGenService = new IDGeneratorService();
				$idGenService->setEntity($employee);
				$employee->setEmpNumber($idGenService->getNextID() );
        	}
        	$employee->save();

        } catch (Exception $e) {
            throw new AdminServiceException($e->getMessage());
        }
    }

    /**
     * Get employee with given empNumber
     * @param int $empNumber Employee number
     * @return Employee Employee object
     */
    public function getEmployee($empNumber) {
   	 	try {
	    	$employee = Doctrine::getTable('Employee')->find($empNumber);
	    	return $employee;
        } catch (Exception $e) {
            throw new AdminServiceException($e->getMessage());
        }
    }

    /**
     * Get the default employee id to be used for next employee being
     * added to the system.
     *
     * @return employee id based on empNumber
     */
    public function getDefaultEmployeeId() {
    	$idGenService = new IDGeneratorService();
    	$idGenService->setEntity(new Employee());
    	return $idGenService->getNextID(false);
    }
    
    public function getEmployeeListAsJson() {
     	
     	try {
        	$jsonString	=	array();
        	$q = Doctrine_Query::create()
			    ->from('Employee');
			
			$employeeList  = $q->execute();
			$escapeCharSet = array(38, 39, 34, 60, 61,62, 63, 64, 58, 59, 94, 96);
			foreach( $employeeList as $employee) {
            $name = $employee->getFirstName() . " " . $employee->getLastName();
            foreach($escapeCharSet as $char) {
               $name = str_replace(chr($char), (chr(92) . chr($char)), $name);
            }
				array_push($jsonString,"{name:'". $name ."',id:'".$employee->getEmpNumber()."'}");
			}
			
			$jsonStr = " [".implode(",",$jsonString)."]";
			return $jsonStr;
			
        } catch(Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
        
    }
    
    public function getSupervisorListAsJson($empId) {
     	
     	try {
			$jsonString	=	array();
        	$q = Doctrine_Query::create()
			    ->from('ReportTo rt')
			    ->where("rt.subordinateId = $empId");
			
			$supervisorList = $q->execute();
			   
			foreach($supervisorList as $supervisor) {
				array_push($jsonString,"{name:'".$supervisor->supervisor->getFullName()."',id:'".$supervisor->supervisor->getEmpNumber()."'}");
			}
			
			$jsonStr	=	" [".implode(",",$jsonString)."]";
			
			return $jsonStr;
			
        }catch( Exception $e) {
           throw new PIMServiceException($e->getMessage());
        }
    }
    
    public function getSupervisorListAsString($empId) {
     	
     	try {
     		
			$jsonString	= array();
        	$q = Doctrine_Query::create()
			    ->from('ReportTo rt')
			    ->where("rt.subordinateId = $empId");
			
			$supervisorList = $q->execute();
			
			$supString = ''; 
			$count = count($supervisorList);
			
			for ($i=0; $i<$count; $i++) {
			    
			    $supString .= $supervisorList[$i]->supervisor->getFullName().':'.$supervisorList[$i]->supervisor->getEmpNumber();
			    
			    if ($i < ($count-1)) {
			        $supString .= '-';
			    }
			    
			}
			
			return $supString;
		
        }catch( Exception $e) {
           throw new PIMServiceException($e->getMessage());
        }
    }

}


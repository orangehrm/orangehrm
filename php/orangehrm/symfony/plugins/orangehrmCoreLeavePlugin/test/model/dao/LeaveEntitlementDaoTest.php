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
 * Test for LeavePeriodDao class
 */
class LeaveEntitlementDaoTest extends PHPUnit_Framework_TestCase{
	
	public $leaveEntitlementDao;
	public $leaveType ;
  	public $leavePeriod ;
  	public $employee ;

        protected function setUp() {


                // Save leave type
                $leaveTypeData = sfYaml::load(sfConfig::get('sf_plugins_dir') . '/orangehrmCoreLeavePlugin/test/fixtures/leaveType.yml');
                $leaveTypeDao	=	new LeaveTypeDao();
                $leaveType	=	new LeaveType();
                $leaveType->setLeaveTypeName($leaveTypeData['leaveType']['LT_001']['name']);
//                $leaveType->setLeaveRules($leaveTypeData['leaveType']['LT_001']['rule']);
                $leaveTypeDao->saveLeaveType( $leaveType );
                $this->leaveType	=	$leaveType;

                // Save leave Period
                $leavePeriodData = sfYaml::load(sfConfig::get('sf_plugins_dir') . '/orangehrmCoreLeavePlugin/test/fixtures/leavePeriod.yml');
                $leavePeriodService	=	new LeavePeriodService();
                $leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
                $leavePeriod = new LeavePeriod();
                $leavePeriod->setStartDate($leavePeriodData['leavePeriod']['1']['startDate']);
                $leavePeriod->setEndDate($leavePeriodData['leavePeriod']['1']['endDate']);
                $leavePeriodService->saveLeavePeriod($leavePeriod);
                $this->leavePeriod		=	$leavePeriod;

                // Save Employee
                $employeeservice		=	new EmployeeService();
                $this->employee			=	new Employee();
                $employeeservice->addEmployee($this->employee);

                // save leave quota
                $this->leaveEntitlement = sfYaml::load(sfConfig::get('sf_plugins_dir') . '/orangehrmCoreLeavePlugin/test/fixtures/leaveEntitlement.yml');
                $this->leaveEntitlementDao = new LeaveEntitlementDao();
                


        }

        public function tearDown(){

            $q = Doctrine_Query::create()
                        ->delete('Employee em')
                        ->where('em.empNumber=?',$this->employee->getEmpNumber());

             $q->execute ();

             $q = Doctrine_Query::create()
                        ->delete('LeaveType lt')
                        ->where('lt.leaveTypeId=?', $this->leaveType->getLeaveTypeId());

             $q->execute ();

             $q = Doctrine_Query::create()
                        ->delete('LeavePeriod lp')
                        ->where('lp.leavePeriodId=?',$this->leavePeriod->getLeavePeriodId());

             $q->execute ();

        }
	
	/**
	 * 
	 * @cover getEmployeeLeaveEntitlement
	 */
	public function testGetEmployeeLeaveEntitlement(){
		$result = $this->leaveEntitlementDao->getEmployeeLeaveEntitlement($this->employee->getEmpNumber(),$this->leaveType->getLeaveTypeId(),$this->leavePeriod->getLeavePeriodId());
		$this->assertFalse($result);
		
	}
	
	/**
	 * @cover saveEmployeeLeaveEntitlement
	 * @return unknown_type
	 */
	public function testSaveEmployeeLeaveEntitlement(){
		$result = $this->leaveEntitlementDao->saveEmployeeLeaveEntitlement( $this->employee->getEmpNumber(),$this->leaveType->getLeaveTypeId(),$this->leavePeriod->getLeavePeriodId() ,10);
		$this->assertTrue($result);
	}
	
	/**
	 * @expectedException DaoException
	 */
	public function testSaveEmployeeLeaveEntitlementForEmpty(){
		$result = $this->leaveEntitlementDao->saveEmployeeLeaveEntitlement( null,null,null ,null);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function testReadEmployeeLeaveEntitlement(){
		$this->leaveEntitlementDao->saveEmployeeLeaveEntitlement( $this->employee->getEmpNumber(),$this->leaveType->getLeaveTypeId(),$this->leavePeriod->getLeavePeriodId() ,7);
		$result = $this->leaveEntitlementDao->readEmployeeLeaveEntitlement( $this->employee->getEmpNumber(),$this->leaveType->getLeaveTypeId(),$this->leavePeriod->getLeavePeriodId());
		$this->assertType('EmployeeLeaveEntitlement',$result);
	}
	
	/**
	 * @cover overwriteEmployeeLeaveEntitlement
	 * @return unknown_type
	 */
	public function testOverwriteEmployeeLeaveEntitlement(){
		$this->leaveEntitlementDao->saveEmployeeLeaveEntitlement( $this->employee->getEmpNumber(),$this->leaveType->getLeaveTypeId(),$this->leavePeriod->getLeavePeriodId() ,10);
		$result = $this->leaveEntitlementDao->overwriteEmployeeLeaveEntitlement( $this->employee->getEmpNumber(),$this->leaveType->getLeaveTypeId(),$this->leavePeriod->getLeavePeriodId(),12);
		$this->assertTrue($result);

        }


        /**
         * check for null leave carried forward
         * @covers LeaveEntitlementDao::saveEmployeeLeaveCarriedForward
         * @return void
         */
        public function testSaveEmployeeLeaveCarriedForwardForEmpty(){
            try{
                $result = $this->leaveEntitlementDao->saveEmployeeLeaveCarriedForward(null,null, null, 10);
            }
            catch(DaoException $e){
                return true;
            }

            $this->fail('An Expected exception was not returned');
        }

        /**
         * check for null leave Brought forward
         * @covers LeaveEntitlementDao::saveEmployeeLeaveBroughtForward
         * @return void
         */
        public function testSaveEmployeeLeaveBroughtForwardForEmpty(){
            try{
                $result = $this->leaveEntitlementDao->saveEmployeeLeaveBroughtForward(null,null, null, 10);
            }
            catch(DaoException $e){
                return true;
            }

            $this->fail('An Expected exception was not returned');
        }


        /**
         *
         * @covers LeaveEntitlementDao::saveEmployeeLeaveCarriedForward
         * @return void
         */
        public function testSaveEmployeeLeaveCarriedForward(){
            $this->leaveEntitlementDao->saveEmployeeLeaveEntitlement( $this->employee->getEmpNumber(),$this->leaveType->getLeaveTypeId(),$this->leavePeriod->getLeavePeriodId() ,10);
            $result = $this->leaveEntitlementDao->saveEmployeeLeaveCarriedForward( $this->employee->getEmpNumber(),$this->leaveType->getLeaveTypeId(),$this->leavePeriod->getLeavePeriodId(),12);
            $this->assertTrue($result);
        }
        
        /**
         *
         * @covers LeaveEntitlementDao::saveEmployeeLeaveBroughtForward
         * @return void
         */
        public function testSaveEmployeeLeaveBroughtForward(){
            $this->leaveEntitlementDao->saveEmployeeLeaveEntitlement( $this->employee->getEmpNumber(),$this->leaveType->getLeaveTypeId(),$this->leavePeriod->getLeavePeriodId() ,10);
            $result = $this->leaveEntitlementDao->saveEmployeeLeaveBroughtForward( $this->employee->getEmpNumber(),$this->leaveType->getLeaveTypeId(),$this->leavePeriod->getLeavePeriodId(),12);
            $this->assertTrue($result);
        }

}
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
 * Description of SystemUserServiceTest
 *
 */
class SystemUserServiceTest extends PHPUnit_Framework_TestCase {
    
    /** @property SystemUserService $systemUserService */
    private $systemUserService;

    /**
     * Set up method
     */
    protected function setUp() {
        $this->systemUserService = new SystemUserService();
    }
    
    /**
     * @covers SystemUserService::getNonPredefinedUserRoles
     */  
    public function testGetNonPredefinedUserRoles() {
        $userRoles = new Doctrine_Collection('UserRole');

        for ($i = 0; $i < 2; $i++) {
            $userRole = new UserRole();
            $userRole->setId($i+1);
            $userRole->setName("test name".$i+1);
            $userRole->setIsAssignable(1);
            $userRole->setIsPredefined(0);
            $userRoles->add($userRole);
        }
        
        $dao = $this->getMock('SystemUserDao');
        
        $dao->expects($this->once())
             ->method('getNonPredefinedUserRoles')
             ->will($this->returnValue($userRoles));
        
        $this->systemUserService->setSystemUserDao($dao);
        $result = $this->systemUserService->getNonPredefinedUserRoles();
        
         $this->assertEquals($userRoles, $result);
    }
}


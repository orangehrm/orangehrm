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

namespace OrangeHRM\Pim\Tests\Service;


use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;
use OrangeHRM\PIM\Dao\EmpEmergencyContactDao;
use OrangeHRM\PIM\Dto\EmpEmergencyContactSearchFilterParams;
use OrangeHRM\PIM\Service\EmpEmergencyContactService;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\EmpEmergencyContact;
use OrangeHRM\Core\Service\ConfigService;


/**
 * @group Pim
 * @group Service
 */
class EmpEmergencyContactServiceTest extends TestCase
{
    /**
     * @var EmpEmergencyContactService
     */
    private EmpEmergencyContactService $emergencyContactService;
    private string $fixture;


    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->emergencyContactService = new EmpEmergencyContactService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/EmpDependentDao.yml';
        TestDataService::populate($this->fixture);
    }

//    /**
//     * Testing deleteEmergencyContacts
//     */
//    public function testDeleteEmployeeEmergencyContacts():void
//    {
//        $empNumber = 1;
//        $contactsToDelete = ['1', '2'];
//        $empContactDao = $this->getMockBuilder(EmpEmergencyContactDao::class)->getMock();
//        $empContactDao->expects($this->once())
//            ->method('deleteEmployeeEmergencyContacts')
//            ->with($empNumber, $contactsToDelete)
//            ->will($this->returnValue(true));
//
//        $this->emergencyContactService->setEmpEmergencyContactDao($empContactDao);
//
//        $result = $this->emergencyContactService->deleteEmployeeEmergencyContacts($empNumber, $contactsToDelete);
//        $this->assertTrue($result);
//
//    }

}

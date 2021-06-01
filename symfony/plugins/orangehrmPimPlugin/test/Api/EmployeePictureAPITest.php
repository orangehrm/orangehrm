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

namespace OrangeHRM\Tests\Pim\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmpPicture;
use OrangeHRM\Pim\Api\EmployeePictureAPI;
use OrangeHRM\Pim\Service\EmployeePictureService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;

/**
 * @group Pim
 * @group APIv2
 */
class EmployeePictureAPITest extends EndpointTestCase
{
    public function testGetOne(): void
    {
        $empNumber = 1;
        $employeePictureService = $this->getMockBuilder(EmployeePictureService::class)
            ->onlyMethods(['getEmpPictureByEmpNumber'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $empPicture = new EmpPicture();
        $empPicture->setEmployee($employee);
        $empPicture->setFileType('text/plain');
        $empPicture->setFilename('attachment.txt');

        $employeePictureService->expects($this->exactly(2))
            ->method('getEmpPictureByEmpNumber')
            ->willReturn($empPicture, null);

        /** @var MockObject&EmployeePictureAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeePictureAPI::class,
            [
                RequestParams::PARAM_TYPE_QUERY => [],
                RequestParams::PARAM_TYPE_BODY => [],
                RequestParams::PARAM_TYPE_ATTRIBUTE => [CommonParams::PARAMETER_EMP_NUMBER => $empNumber]
            ]
        )->onlyMethods(['getEmployeePictureService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getEmployeePictureService')
            ->will($this->returnValue($employeePictureService));

        $result = $api->getOne();
        $this->assertEquals(
            [
                'empNumber' => $empNumber,
                'filename' => 'attachment.txt',
                'fileType' => 'text/plain',
                'size' => null,
                'width' => null,
                'height' => null,
            ],
            $result->normalize()
        );

        $this->expectException(RecordNotFoundException::class);
        $api->getOne();
    }
}

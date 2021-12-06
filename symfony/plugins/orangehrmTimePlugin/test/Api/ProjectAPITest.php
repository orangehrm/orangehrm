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

namespace OrangeHRM\Tests\Time\Api;

use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Api\V2\Exception\NotImplementedException;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Serializer\NormalizeException;
use OrangeHRM\Entity\Project;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;
use OrangeHRM\Time\Api\ProjectAPI;
use OrangeHRM\Time\Dao\ProjectDao;
use OrangeHRM\Time\Service\ProjectService;

class ProjectAPITest extends EndpointTestCase
{
    /**
     * @throws NormalizeException
     * @throws NotImplementedException
     * @throws RecordNotFoundException
     * @throws ForbiddenException
     */
    public function testCreate(): void
    {
        $projectDao = $this->getMockBuilder(ProjectDao::class)
            ->onlyMethods(['saveProject'])
            ->getMock();
        $projectDao->expects($this->once())
            ->method('saveProject')
            ->will(
                $this->returnCallback(
                    function (Project $project) {
                        $project->setId(2);
                        return $project;
                    }
                )
            );
        $projectService = $this->getMockBuilder(ProjectService::class)
            ->onlyMethods(['getProjectDao'])
            ->getMock();
        $projectService->expects($this->once())
            ->method('getProjectDao')
            ->willReturn($projectDao);

        /**
         * @var MockObject&ProjectAPI $api
         */
        $api = $this->getApiEndpointMockBuilder(
            ProjectAPI::class,
            [
                RequestParams::PARAM_TYPE_BODY => [
                    ProjectAPI::PARAMETER_NAME => 'sample_project',
                    ProjectAPI::PARAMETER_DESCRIPTION => 'sample_project_description',
                    ProjectAPI::PARAMETER_CUSTOMER_ID => 1,
                    ProjectAPI::PARAMETER_PROJECT_ADMINS => [1, 2],
                    ProjectAPI::PARAMETER_IS_DELETED => false
                ]
            ]
        )->onlyMethods(['getProjectService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getProjectService')
            ->will($this->returnValue($projectService));
        $result = $api->create();
        $this->assertEquals(
            [
                "id" => 2,
                "name" => 'sample_project',
                "description" => 'sample_project_description',
                "customerId" => 1,
                "projectAdmins" => [1, 2],
                "deleted" => false
            ],
            $result->normalize()
        );
    }

    public function testGetAll(): void
    {
        $projectDao = $this->getMockBuilder(ProjectDao::class)
            ->onlyMethods(['getAllProjects', 'searchProjectsCount'])
            ->getMock();
        $project1 = new Project();
        $project1->setId(2);
        $project1->setName('Test02');
        $project1->setDescription('TestDes02');
        $project1->setIsDeleted(false);
        $project1->getDecorator()->setCustomerById(1);
        $project1->getDecorator()->setProjectAdminsByEmpNumbers([1,2]);
        
        $project2 = new Project();
        $project2->setId(3);
        $project2->setName('Test03');
        $project2->setDescription('TestDes03');
        $project2->setIsDeleted(false);
        $project2->getDecorator()->setCustomerById(1);
        $project2->getDecorator()->setProjectAdminsByEmpNumbers([1,2]);

        $projectDao->expects($this->exactly(1))
            ->method('getAllProjects')
            ->willReturn([$project1, $project2]);
        $projectDao->expects($this->exactly(1))
            ->method('searchProjectsCount')
            ->willReturn(2);
        $projectService = $this->getMockBuilder(ProjectService::class)
            ->onlyMethods(['getProjectDao'])
            ->getMock();
        $projectService->expects($this->exactly(2))
            ->method('getProjectDao')
            ->willReturn($projectDao);

        /** @var MockObject&ProjectAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            ProjectAPI::class,
            [
                RequestParams::PARAM_TYPE_BODY => [
                    ProjectAPI::PARAMETER_NAME,
                ]
            ]
        )->onlyMethods(['getProjectService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getProjectService')
            ->will($this->returnValue($projectService));

        $result = $api->getAll();
        $this->assertEquals(
            [
                [
                    "id" => 3,
                    "name" => 'CUSGET1',
                    "description" => 'CUSGETDES1',
                    "deleted" => false
                ],
                [
                    "id" => 4,
                    "name" => 'CUSGET2',
                    "description" => 'CUSGETDES2',
                    "deleted" => false
                ]
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                "total" => 2
            ],
            $result->getMeta()->all()
        );
    }

    public function testGetOne(): void
    {
        $projectDao = $this->getMockBuilder(CustomerDao::class)
            ->onlyMethods(['getCustomerById'])
            ->getMock();
        $customer = new Project();
        $customer->setId(4);
        $customer->setName('CUS10');
        $customer->setDescription('DESC10');
        $customer->setDeleted(false);

        $projectDao->expects($this->exactly(1))
            ->method('getCustomerById')
            ->with(1)
            ->will($this->returnValue($customer));
        $projectService = $this->getMockBuilder(CustomerService::class)
            ->onlyMethods(['getCustomerDao'])
            ->getMock();
        $projectService->expects($this->exactly(1))
            ->method('getCustomerDao')
            ->willReturn($projectDao);

        /** @var MockObject&CustomerAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            CustomerAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_ID => 1
                ]
            ]
        )->onlyMethods(['getProjectService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getProjectService')
            ->will($this->returnValue($projectService));

        $result = $api->getOne();
        $this->assertEquals(
            [
                "id" => 4,
                "name" => "CUS10",
                "description" => "DESC10",
                "deleted" => false
            ],
            $result->normalize()
        );
    }

    public function testUpdate(): void
    {
        $projectDao = $this->getMockBuilder(CustomerDao::class)
            ->onlyMethods(['saveCustomer', 'getCustomerById'])
            ->getMock();
        $customer = new Project();
        $customer->setId(1);
        $customer->setName('Dev');
        $customer->setDescription('Dev');
        $customer->setDeleted(false);
        $projectDao->expects($this->exactly(1))
            ->method('getCustomerById')
            ->with(1)
            ->willReturn($customer);
        $projectDao->expects($this->exactly(1))
            ->method('saveCustomer')
            ->with($customer)
            ->will($this->returnValue($customer));
        $projectService = $this->getMockBuilder(CustomerService::class)
            ->onlyMethods(['getCustomerDao'])
            ->getMock();
        $projectService->expects($this->exactly(2))
            ->method('getCustomerDao')
            ->willReturn($projectDao);

        /** @var MockObject&CustomerAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            CustomerAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_ID => 1
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    CustomerAPI::PARAMETER_NAME => 'COVID',
                    CustomerAPI::PARAMETER_DESCRIPTION => 'COVID',
                    CustomerAPI::PARAMETER_DELETED => false
                ]
            ]
        )->onlyMethods(['getProjectService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getProjectService')
            ->will($this->returnValue($projectService));

        $result = $api->update();
        $this->assertEquals(
            [
                "id" => 1,
                "name" => "COVID",
                "description" => 'COVID',
                "deleted" => false

            ],
            $result->normalize()
        );
    }

    public function testDelete(): void
    {
        $projectDao = $this->getMockBuilder(CustomerDao::class)
            ->onlyMethods(['deleteCustomer'])
            ->getMock();

        $customer = new Project();
        $customer->setId(1);
        $customer->setName('Dev');
        $customer->setDescription('Dev');
        $customer->setDeleted(false);

        $projectDao->expects($this->exactly(1))
            ->method('deleteCustomer')
            ->with([1])
            ->willReturn(1);
        $projectService = $this->getMockBuilder(CustomerService::class)
            ->onlyMethods(['getCustomerDao'])
            ->getMock();
        $projectService->expects($this->exactly(1))
            ->method('getCustomerDao')
            ->willReturn($projectDao);

        /** @var MockObject&CustomerAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            CustomerAPI::class,
            [

                RequestParams::PARAM_TYPE_BODY => [
                    CommonParams::PARAMETER_IDS => [1],
                ]
            ]
        )->onlyMethods(['getProjectService'])
            ->getMock();
        $api->expects($this->exactly(1))
            ->method('getProjectService')
            ->will($this->returnValue($projectService));

        $result = $api->delete();
        $this->assertEquals(
            [
                1
            ],
            $result->normalize()
        );
    }
}

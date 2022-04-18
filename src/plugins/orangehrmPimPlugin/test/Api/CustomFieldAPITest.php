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
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Entity\CustomField;
use OrangeHRM\Pim\Api\CustomFieldAPI;
use OrangeHRM\Pim\Dao\CustomFieldDao;
use OrangeHRM\Pim\Service\CustomFieldService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;

/**
 * @group Pim
 * @group APIv2
 */
class CustomFieldAPITest extends EndpointTestCase
{
    public function testGetCustomFieldService(): void
    {
        $api = new CustomFieldAPI($this->getRequest());
        $this->assertTrue($api->getCustomFieldService() instanceof CustomFieldService);
    }

    public function testGetOne(): void
    {
        $customFieldDao = $this->getMockBuilder(CustomFieldDao::class)
            ->onlyMethods(['getCustomFieldById'])
            ->getMock();

        $customField = new CustomField();
        $customField->setFieldNum(1);
        $customField->setName('Level');
        $customField->setType(1);
        $customField->setScreen('Personal');
        $customField->setExtraData('level1, level2');

        $customFieldDao->expects($this->exactly(1))
            ->method('getCustomFieldById')
            ->with(1)
            ->will($this->returnValue($customField));

        $customFieldService = $this->getMockBuilder(CustomFieldService::class)
            ->onlyMethods(['getCustomFieldDao'])
            ->getMock();

        $customFieldService->expects($this->exactly(1))
            ->method('getCustomFieldDao')
            ->willReturn($customFieldDao);

        /** @var MockObject&CustomFieldAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            CustomFieldAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_ID => 1
                ]
            ]
        )->onlyMethods(['getCustomFieldService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getCustomFieldService')
            ->will($this->returnValue($customFieldService));

        $result = $api->getOne();
        $this->assertEquals(
            [
                "id" => 1,
                "fieldName" => "Level",
                "fieldType" => 1,
                "extraData" => 'level1, level2',
                "screen" => "Personal"
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForGetOne(): void
    {
        $api = new CustomFieldAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetOne();
        $this->assertTrue(
            $this->validate(
                [CommonParams::PARAMETER_ID => 1],
                $rules
            )
        );
    }

    public function testUpdate()
    {
        $customFieldDao = $this->getMockBuilder(CustomFieldDao::class)
            ->onlyMethods(['saveCustomField', 'getCustomFieldById'])
            ->getMock();

        $customField = new CustomField();
        $customField->setFieldNum(1);
        $customField->setName('Level');
        $customField->setType(1);
        $customField->setScreen('Personal');
        $customField->setExtraData('level1, level2');

        $customFieldDao->expects($this->exactly(1))
            ->method('getCustomFieldById')
            ->with(1)
            ->willReturn($customField);

        $customFieldDao->expects($this->exactly(1))
            ->method('saveCustomField')
            ->will(
                $this->returnCallback(
                    function (CustomField $customField) {
                        return $customField;
                    }
                )
            );

        $customFieldService = $this->getMockBuilder(CustomFieldService::class)
            ->onlyMethods(['getCustomFieldDao'])
            ->getMock();

        $customFieldService->expects($this->exactly(2))
            ->method('getCustomFieldDao')
            ->willReturn($customFieldDao);

        /** @var MockObject&CustomFieldAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            CustomFieldAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_ID => 1
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    CustomFieldAPI::PARAMETER_NAME => "Level",
                    CustomFieldAPI::PARAMETER_TYPE => 1,
                    CustomFieldAPI::PARAMETER_SCREEN => "Personal",
                    CustomFieldAPI::PARAMETER_EXTRA_DATA => 'level1, level2',
                ]
            ]
        )->onlyMethods(['getCustomFieldService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getCustomFieldService')
            ->will($this->returnValue($customFieldService));

        $result = $api->update();
        $this->assertEquals(
            [
                "id" => 1,
                "fieldName" => "Level",
                "fieldType" => 1,
                "extraData" => 'level1, level2',
                "screen" => "Personal"
            ],
            $result->normalize()
        );
    }

    public function testUpdateChangeExtraData()
    {
        $customFieldDao = $this->getMockBuilder(CustomFieldDao::class)
            ->onlyMethods(['saveCustomField', 'getCustomFieldById','isCustomFieldInUse'])
            ->getMock();

        $customField = new CustomField();
        $customField->setFieldNum(1);
        $customField->setName('Level');
        $customField->setType(1);
        $customField->setScreen('Personal');
        $customField->setExtraData('level1, level2');

        $customFieldDao->expects($this->exactly(1))
            ->method('getCustomFieldById')
            ->with(1)
            ->willReturn($customField);

        $customFieldService = $this->getMockBuilder(CustomFieldService::class)
            ->onlyMethods(['getCustomFieldDao','deleteRelatedEmployeeCustomFieldsExtraData'])
            ->getMock();

        $customFieldService->expects($this->exactly(2))
            ->method('getCustomFieldDao')
            ->willReturn($customFieldDao);
        $customFieldService->expects($this->exactly(1))
            ->method('deleteRelatedEmployeeCustomFieldsExtraData');

        /** @var MockObject&CustomFieldAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            CustomFieldAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_ID => 1
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    CustomFieldAPI::PARAMETER_NAME => "Level",
                    CustomFieldAPI::PARAMETER_TYPE => 1,
                    CustomFieldAPI::PARAMETER_SCREEN => "Personal",
                    CustomFieldAPI::PARAMETER_EXTRA_DATA => 'level1',
                ]
            ]
        )->onlyMethods(['getCustomFieldService'])
            ->getMock();
        $api->expects($this->exactly(3))
            ->method('getCustomFieldService')
            ->will($this->returnValue($customFieldService));

        $result = $api->update();
        $this->assertEquals(
            [
                "id" => 1,
                "fieldName" => "Level",
                "fieldType" => 1,
                "extraData" => 'level1',
                "screen" => "Personal"
            ],
            $result->normalize()
        );
    }

    public function testUpdateChangeType()
    {
        $customFieldDao = $this->getMockBuilder(CustomFieldDao::class)
            ->onlyMethods(['saveCustomField', 'getCustomFieldById','isCustomFieldInUse'])
            ->getMock();

        $customField = new CustomField();
        $customField->setFieldNum(1);
        $customField->setName('Level');
        $customField->setType(1);
        $customField->setScreen('Personal');
        $customField->setExtraData('level1, level2');

        $customFieldDao->expects($this->exactly(1))
            ->method('getCustomFieldById')
            ->with(1)
            ->willReturn($customField);

        $customFieldDao->expects($this->exactly(1))
            ->method('isCustomFieldInUse')
            ->with(1)
            ->willReturn(true);

        $customFieldService = $this->getMockBuilder(CustomFieldService::class)
            ->onlyMethods(['getCustomFieldDao','deleteRelatedEmployeeCustomFieldsExtraData'])
            ->getMock();

        $customFieldService->expects($this->exactly(2))
            ->method('getCustomFieldDao')
            ->willReturn($customFieldDao);
        $customFieldService->expects($this->exactly(0))
            ->method('deleteRelatedEmployeeCustomFieldsExtraData');

        /** @var MockObject&CustomFieldAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            CustomFieldAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_ID => 1
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    CustomFieldAPI::PARAMETER_NAME => "Level",
                    CustomFieldAPI::PARAMETER_TYPE => 0,
                    CustomFieldAPI::PARAMETER_SCREEN => "Personal",
                    CustomFieldAPI::PARAMETER_EXTRA_DATA => 'level1, level2',
                ]
            ]
        )->onlyMethods(['getCustomFieldService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getCustomFieldService')
            ->will($this->returnValue($customFieldService));
        $this->expectBadRequestException();
        $api->update();
    }

    public function testGetValidationRuleForUpdate(): void
    {
        $api = new CustomFieldAPI($this->getRequest());
        $rules = $api->getValidationRuleForUpdate();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_ID => 1,
                    CustomFieldAPI::PARAMETER_NAME => "Level",
                    CustomFieldAPI::PARAMETER_TYPE => 1,
                    CustomFieldAPI::PARAMETER_SCREEN => "personal",
                    CustomFieldAPI::PARAMETER_EXTRA_DATA => 'level1, level2',
                ],
                $rules
            )
        );
    }

    public function testDelete()
    {
        $customFieldDao = $this->getMockBuilder(CustomFieldDao::class)
            ->onlyMethods(['deleteCustomFields'])
            ->getMock();

        $customFieldService = $this->getMockBuilder(CustomFieldService::class)
            ->onlyMethods(['getCustomFieldDao','getAllFieldsInUse'])
            ->getMock();

        $customFieldService->expects($this->exactly(0))
            ->method('getCustomFieldDao')
            ->willReturn($customFieldDao);

        $customFieldService->expects($this->exactly(1))
            ->method('getAllFieldsInUse')
            ->willReturn([1,5]);

        /** @var MockObject&CustomFieldAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            CustomFieldAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [],
                RequestParams::PARAM_TYPE_BODY => [
                    CommonParams::PARAMETER_IDS => [1],
                ]
            ]
        )->onlyMethods(['getCustomFieldService'])
            ->getMock();
        $api->expects($this->exactly(1))
            ->method('getCustomFieldService')
            ->will($this->returnValue($customFieldService));
        $this->expectBadRequestException();
        $result = $api->delete();
    }

    public function testDeleteNoInUse()
    {
        $customFieldDao = $this->getMockBuilder(CustomFieldDao::class)
            ->onlyMethods(['deleteCustomFields'])
            ->getMock();

        $customFieldService = $this->getMockBuilder(CustomFieldService::class)
            ->onlyMethods(['getCustomFieldDao','getAllFieldsInUse'])
            ->getMock();

        $customFieldService->expects($this->exactly(1))
            ->method('getCustomFieldDao')
            ->willReturn($customFieldDao);

        $customFieldService->expects($this->exactly(1))
            ->method('getAllFieldsInUse')
            ->willReturn([1,5]);

        /** @var MockObject&CustomFieldAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            CustomFieldAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [],
                RequestParams::PARAM_TYPE_BODY => [
                    CommonParams::PARAMETER_IDS => [2],
                ]
            ]
        )->onlyMethods(['getCustomFieldService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getCustomFieldService')
            ->will($this->returnValue($customFieldService));

        $result = $api->delete();
        $this->assertEquals(
            [
                2
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new CustomFieldAPI($this->getRequest());
        $rules = $api->getValidationRuleForDelete();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_IDS => [1],
                ],
                $rules
            )
        );
    }

    public function testCreate()
    {
        $customFieldDao = $this->getMockBuilder(CustomFieldDao::class)
            ->onlyMethods(['saveCustomField', 'getCustomFieldById'])
            ->getMock();

        $customField = new CustomField();
        $customField->setName('Level');
        $customField->setType(1);
        $customField->setScreen('Personal');
        $customField->setExtraData('level1, level2');


        $customFieldDao->expects($this->never())
            ->method('getCustomFieldById')
            ->with(1)
            ->willReturn($customField);

        $customFieldDao->expects($this->once())
            ->method('saveCustomField')
            ->will(
                $this->returnCallback(
                    function (CustomField $customField) {
                        $customField->setFieldNum(1);
                        return $customField;
                    }
                )
            );

        $customFieldService = $this->getMockBuilder(CustomFieldService::class)
            ->onlyMethods(['getCustomFieldDao'])
            ->getMock();

        $customFieldService->expects($this->exactly(2))
            ->method('getCustomFieldDao')
            ->willReturn($customFieldDao);

        /** @var MockObject&CustomFieldAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            CustomFieldAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    CustomFieldAPI::PARAMETER_NAME => "Level",
                    CustomFieldAPI::PARAMETER_TYPE => 1,
                    CustomFieldAPI::PARAMETER_SCREEN => "Personal",
                    CustomFieldAPI::PARAMETER_EXTRA_DATA => 'level1, level2',
                ]
            ]
        )->onlyMethods(['getCustomFieldService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getCustomFieldService')
            ->will($this->returnValue($customFieldService));

        $result = $api->create();
        $this->assertEquals(
            [
                "id" => 1,
                "fieldName" => "Level",
                "fieldType" => 1,
                "extraData" => 'level1, level2',
                "screen" => "Personal"
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new CustomFieldAPI($this->getRequest());
        $rules = $api->getValidationRuleForCreate();
        $this->assertTrue(
            $this->validate(
                [
                    CustomFieldAPI::PARAMETER_NAME => "Level",
                    CustomFieldAPI::PARAMETER_TYPE => 1,
                    CustomFieldAPI::PARAMETER_SCREEN => "personal",
                    CustomFieldAPI::PARAMETER_EXTRA_DATA => 'level1, level2',
                ],
                $rules
            )
        );
    }


    public function testGetAll()
    {
        $customFieldDao = $this->getMockBuilder(CustomFieldDao::class)
            ->onlyMethods(['searchCustomField', 'getSearchCustomFieldsCount'])
            ->getMock();

        $customField1 = new CustomField();
        $customField1->setFieldNum(1);
        $customField1->setName('Level');
        $customField1->setType(1);
        $customField1->setScreen('Personal');
        $customField1->setExtraData('level1, level2');

        $customField2 = new CustomField();
        $customField2->setFieldNum(2);
        $customField2->setName('Level');
        $customField2->setType(0);
        $customField2->setScreen('Personal');

        $customFieldDao->expects($this->exactly(1))
            ->method('searchCustomField')
            ->willReturn([$customField1, $customField2]);

        $customFieldDao->expects($this->exactly(1))
            ->method('getSearchCustomFieldsCount')
            ->willReturn(2);

        $customFieldService = $this->getMockBuilder(CustomFieldService::class)
            ->onlyMethods(['getCustomFieldDao'])
            ->getMock();

        $customFieldService->expects($this->exactly(2))
            ->method('getCustomFieldDao')
            ->willReturn($customFieldDao);

        /** @var MockObject&CustomFieldAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            CustomFieldAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => []
            ]
        )->onlyMethods(['getCustomFieldService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getCustomFieldService')
            ->will($this->returnValue($customFieldService));

        $result = $api->getAll();
        $this->assertEquals(
            [
                [
                    "id" => 1,
                    "fieldName" => "Level",
                    "fieldType" => 1,
                    "extraData" => 'level1, level2',
                    "screen" => "Personal"
                ],
                [
                    "id" => 2,
                    "fieldName" => "Level",
                    "fieldType" => 0,
                    "extraData" => null,
                    "screen" => "Personal"
                ]
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForGetAll(): void
    {
        $api = new CustomFieldAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetAll();
        $this->assertTrue(
            $this->validate(
                [],
                $rules
            )
        );
    }
}

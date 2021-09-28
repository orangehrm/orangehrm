<?php
/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms and conditions on using this software.
 *
 */

namespace OrangeHRM\Tests\Pim\Api;

use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Api\EmployeeCSVImportAPI;
use OrangeHRM\Pim\Service\PimCsvDataImportService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;

/**
 * @group Pim
 * @group APIv2
 */
class EmployeeCSVImportAPITest extends EndpointTestCase
{
    public function testDelete(): void
    {
        $api = new EmployeeCSVImportAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new EmployeeCSVImportAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }

    public function testGetAll(): void
    {
        $api = new EmployeeCSVImportAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getAll();
    }

    public function testGetValidationRuleForGetAll(): void
    {
        $api = new EmployeeCSVImportAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForGetAll();
    }

    public function testGetValidationRuleForCreate(): void
    {
        $configService = $this->getMockBuilder(ConfigService::class)
                              ->onlyMethods(['getAllowedFileTypes', 'getMaxAttachmentSize'])
                              ->getMock();
        $configService->expects($this->once())
                                    ->method('getMaxAttachmentSize')
                                    ->will($this->returnValue(1048576));

        $this->createKernelWithMockServices(
            [
                Services::CONFIG_SERVICE => $configService
            ]
        );
        $api = new EmployeeCSVImportAPI($this->getRequest());
        $rules = $api->getValidationRuleForCreate();
        $this->assertTrue(
            $this->validate(
                [
                    EmployeeCSVImportAPI::PARAMETER_ATTACHMENT => [
                        "name" => 'importData.csv',
                        "type" => 'text/csv',
                        "base64" => "Zmlyc3RfbmFtZSxtaWRkbGVfbmFtZSxsYXN0X25hbWUsZW1wbG95ZWVfaWQsb3RoZXJfaWQsZHJpdmVyJ3NfbGljZW5zZV9ubyxsaWNlbnNlX2V4cGlyeV9kYXRlLGdlbmRlcixtYXJpdGFsX3N0YXR1cyxuYXRpb25hbGl0eSxkYXRlX29mX2JpcnRoLGFkZHJlc3Nfc3RyZWV0XzEsYWRkcmVzc19zdHJlZXRfMixjaXR5LHN0YXRlL3Byb3ZpbmNlLHppcC9wb3N0YWxfY29kZSxjb3VudHJ5LGhvbWVfdGVsZXBob25lLG1vYmlsZSx3b3JrX3RlbGVwaG9uZSx3b3JrX2VtYWlsLG90aGVyX2VtYWlsCkFuZHJldywsUnVzc2VsLEVNUC0wMDMsMTk5MiwyMzQzSkoyMywyMDIyLTEwLTExLE1hbGUsbWFycmllZCxBbWVyaWNhbiwxOTkyLTEwLTAxLDE0MTkgQW5naWUgRHJpdmUsRG93bndhcmQgUGFzc2FnZSxCdXJiYW5rLENhbGlmb3JuaWEsOTE1MDUsVW5pdGVkIFN0YXRlcyw3MTQtOTA2LTAzMzQsMjEzLTkyNi0yMDA4LDIxMy05MjYtMjAwNyx5YXNpcnVAb3JhbmdlaHJtbGl2ZS5jb20seWFzaXJ1bkBvcmFuZ2Vocm1saXZlLmNvbQo=",
                        "size" => "524"
                    ]
                ],
                $rules
            )
        );
    }

    public function testCreate(): void
    {
        $mockPimCsvDataImportService = $this->getMockBuilder(PimCsvDataImportService::class)
                                           ->onlyMethods(['import'])
                                           ->getMock();

        $mockPimCsvDataImportService->expects($this->once())
                                   ->method('import')
                                   ->will($this->returnValue(5));

        /** @var MockObject&EmployeeCSVImportAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeCSVImportAPI::class,
            [
                RequestParams::PARAM_TYPE_BODY => [
                    EmployeeCSVImportAPI::PARAMETER_ATTACHMENT => ["name" => 'importData.csv', "type" => 'text/csv', "base64" => "adsadsad", "size" => 334],
                ],
            ]
        )->onlyMethods(['getPimCsvDataImportService'])
                    ->getMock();
        $api->expects($this->exactly(1))
            ->method('getPimCsvDataImportService')
            ->will($this->returnValue($mockPimCsvDataImportService));
        $result = $api->create();
        $this->assertEquals(
            5,
            $result->getMeta()->get('total')
        );

    }

}

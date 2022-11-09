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

namespace OrangeHRM\Tests\Core\Service;

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Service\ReportGeneratorService;
use OrangeHRM\I18N\Service\I18NHelper;
use OrangeHRM\Pim\Dto\PimReportSearchFilterParams;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Core
 * @group Service
 */
class ReportGeneratorServiceTest extends KernelTestCase
{
    /**
     * @var ReportGeneratorService|null
     */
    private ?ReportGeneratorService $reportGeneratorService = null;

    protected function setUp(): void
    {
        $this->reportGeneratorService = new ReportGeneratorService();
        TestDataService::populate(Config::get(Config::TEST_DIR) . '/phpunit/fixtures/ReportGroup.yaml', true);
        TestDataService::populate(Config::get(Config::TEST_DIR) . '/phpunit/fixtures/FilterField.yaml', true);
        TestDataService::populate(Config::get(Config::TEST_DIR) . '/phpunit/fixtures/DisplayField.yaml', true);
        TestDataService::populate(
            Config::get(Config::PLUGINS_DIR) . '/orangehrmCorePlugin/test/fixtures/ReportGeneratorService.yml',
            true
        );
    }

    public function testIsPimReport(): void
    {
        $this->assertTrue($this->reportGeneratorService->isPimReport(5));
        // Not exists
        $this->assertFalse($this->reportGeneratorService->isPimReport(100));
    }

    public function testGetHeaderDefinitionByReportId(): void
    {
        $i18nHelper = $this->getMockBuilder(I18NHelper::class)
            ->onlyMethods(['transBySource'])
            ->getMock();
        $i18nHelper->method('transBySource')
            ->willReturnCallback(fn ($string) => $string);
        $reportGeneratorService = $this->getMockBuilder(ReportGeneratorService::class)
            ->onlyMethods(['getI18NHelper'])
            ->getMock();
        $reportGeneratorService->method('getI18NHelper')
            ->will($this->returnValue($i18nHelper));
        $header = $reportGeneratorService->getHeaderDefinitionByReportId(5);
        $this->assertEquals(
            [
                [
                    'name' => 'Personal',
                    'children' => [
                        [
                            'name' => 'Employee Id',
                            'prop' => 'employeeId',
                            'size' => 100,
                            'pin' => null,
                            'cellProperties' => null
                        ],
                        [
                            'name' => 'Employee Last Name',
                            'prop' => 'employeeLastname',
                            'size' => 200,
                            'pin' => null,
                            'cellProperties' => null
                        ],
                        [
                            'name' => 'Employee First Name',
                            'prop' => 'employeeFirstname',
                            'size' => 200,
                            'pin' => null,
                            'cellProperties' => null
                        ],
                        [
                            'name' => 'Employee Middle Name',
                            'prop' => 'employeeMiddlename',
                            'size' => 200,
                            'pin' => null,
                            'cellProperties' => null
                        ],
                    ],
                ],
            ],
            $header->normalize()
        );
        $this->assertEquals(
            new ParameterBag(
                [
                    'name' => 'PIM Sample Report',
                    'columnCount' => 4,
                    'groupCount' => 1,
                    'groupedColumnCount' => 4,
                ]
            ),
            $header->getMeta()
        );
    }

    public function testGetNormalizedReportData(): void
    {
        $filterParams = new PimReportSearchFilterParams();
        $filterParams->setReportId(5);
        $reportData = $this->reportGeneratorService->getNormalizedReportData($filterParams);
        $this->assertEquals(
            [
                [
                    'employeeId' => '0001',
                    'employeeLastname' => 'Abbey',
                    'employeeFirstname' => 'Kayla',
                    'employeeMiddlename' => 'T',
                    'empNumber' => 1,
                ],
                [
                    'employeeId' => '0002',
                    'employeeLastname' => 'Abel',
                    'employeeFirstname' => 'Ashley',
                    'employeeMiddlename' => 'ST',
                    'empNumber' => 2,
                ],
            ],
            $reportData
        );
        $this->assertEquals(2, $this->reportGeneratorService->getReportDataCount($filterParams));
    }

    public function testGetNormalizedReportDataForPastEmployees(): void
    {
        $filterParams = new PimReportSearchFilterParams();
        $filterParams->setReportId(6);
        $reportData = $this->reportGeneratorService->getNormalizedReportData($filterParams);

        $this->assertEquals(
            [
                [
                    'employeeLastname' => 'Abraham',
                    'employeeFirstname' => 'Tyler',
                    'empNumber' => 3,
                ],
            ],
            $reportData
        );
        $this->assertEquals(1, $this->reportGeneratorService->getReportDataCount($filterParams));
    }
}

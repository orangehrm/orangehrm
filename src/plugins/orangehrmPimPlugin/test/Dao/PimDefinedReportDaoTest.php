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

namespace OrangeHRM\Tests\Pim\Dao;

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Dao\ReportGeneratorDao;
use OrangeHRM\Entity\Report;
use OrangeHRM\Entity\SelectedFilterField;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\Pim\Dto\PimDefinedReportSearchFilterParams;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Dao
 */
class PimDefinedReportDaoTest extends TestCase
{
    /**
     * @var ReportGeneratorDao
     */
    private ReportGeneratorDao $reportGeneratorDao;

    /**
     * @var string
     */
    protected string $fixture;

    public static function setUpBeforeClass(): void
    {
        TestDataService::populate(Config::get(Config::TEST_DIR) . '/phpunit/fixtures/ReportGroup.yaml', true);
        TestDataService::populate(Config::get(Config::TEST_DIR) . '/phpunit/fixtures/FilterField.yaml', true);
        TestDataService::populate(Config::get(Config::TEST_DIR) . '/phpunit/fixtures/DisplayField.yaml', true);
    }

    protected function setUp(): void
    {
        $this->reportGeneratorDao = new ReportGeneratorDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/ReportGeneratorDao.yaml';
        TestDataService::populate($this->fixture, true);
    }

    public function testGetPimDefinedReportList(): void
    {
        $pimDefinedReportSearchFilterParams = new PimDefinedReportSearchFilterParams();
        $result = $this->reportGeneratorDao->searchPimDefinedReports($pimDefinedReportSearchFilterParams);
        $this->assertCount(2, $result);
        $this->assertTrue($result[0] instanceof Report);
    }

    public function testDeletePimDefinedReportList(): void
    {
        $toTobedeletedIds = [1, 2];
        $result = $this->reportGeneratorDao->deletePimDefinedReport($toTobedeletedIds);
        $this->assertEquals(2, $result);
    }

    public function testGetPimDefinedReportById(): void
    {
        $report = $this->reportGeneratorDao->getReportById(1);
        $this->assertEquals('PIM Sample Report', $report->getName());
        $this->assertEquals(3, $report->getReportGroup()->getId());
        $this->assertEquals('pim', $report->getReportGroup()->getName());
        $this->assertEquals(true, $report->isUseFilterField());
        $this->assertEquals('PIM_DEFINED', $report->getType());
    }

    /**
     * @throws TransactionException
     */
    public function testSavePimDefinedReport(): void
    {
        $report = new Report();
        $reportGroup = $this->reportGeneratorDao->getReportGroupByName('pim');
        $report->setName('PIM Employee Record');
        $report->setReportGroup($reportGroup);
        $report->setUseFilterField(1);
        $report->setType('PIM_DEFINED');
        $selectedDisplayFieldGroupIds = [1, 2];
        $selectedDisplayFieldIds = [1, 2, 3, 4, 5, 6];

        $filterField1 = new SelectedFilterField();
        $filterField1->setX(1);
        $filterField1->setX(null);
        $filterField1->setOperator('eq');
        $filterField1->setReport($report);
        $filterField1->getDecorator()->setFilterFieldById(10);
        $filterField1->setType('Predefined');
        $filterField1->setFilterFieldOrder(1);

        $filterField2 = new SelectedFilterField();
        $filterField2->setX(3);
        $filterField2->setX(null);
        $filterField2->setOperator('eq');
        $filterField2->setReport($report);
        $filterField2->setType('Predefined');
        $filterField2->setFilterFieldOrder(2);
        $filterField2->getDecorator()->setFilterFieldById(9);

        $result = $this->reportGeneratorDao
            ->saveReport(
                $report,
                $selectedDisplayFieldGroupIds,
                $selectedDisplayFieldIds,
                [$filterField1, $filterField2]
            );
        $this->assertTrue($result instanceof Report);
        $this->assertEquals('PIM Employee Record', $report->getName());
        $this->assertEquals(3, $report->getReportGroup()->getId());
        $this->assertEquals(true, $report->isUseFilterField());
        $this->assertEquals('PIM_DEFINED', $report->getType());
        $this->assertEquals(3, $report->getId());
    }

    /**
     * @throws TransactionException
     */
    public function testUpdatePimDefinedReport(): void
    {
        $report = $this->reportGeneratorDao->getReportById(2);
        $reportGroup = $this->reportGeneratorDao->getReportGroupByName('pim');
        $report->setName('PIM Sample Report 2');
        $report->setReportGroup($reportGroup);
        $report->setUseFilterField(1);
        $report->setType('PIM_DEFINED');
        $selectedDisplayFieldGroupIds = [1];
        $selectedDisplayFieldIds = [1, 2, 3, 4, 5, 6];

        $filterField1 = new SelectedFilterField();
        $filterField1->setX(1);
        $filterField1->setX(null);
        $filterField1->setOperator('eq');
        $filterField1->setReport($report);
        $filterField1->getDecorator()->setFilterFieldById(10);
        $filterField1->setType('Predefined');
        $filterField1->setFilterFieldOrder(1);

        $filterField2 = new SelectedFilterField();
        $filterField2->setX(3);
        $filterField2->setX(null);
        $filterField2->setOperator('eq');
        $filterField2->setReport($report);
        $filterField2->setType('Predefined');
        $filterField2->setFilterFieldOrder(2);
        $filterField2->getDecorator()->setFilterFieldById(9);

        $result = $this->reportGeneratorDao
            ->saveReport(
                $report,
                $selectedDisplayFieldGroupIds,
                $selectedDisplayFieldIds,
                [$filterField1, $filterField2]
            );
        $this->assertTrue($result instanceof Report);
        $this->assertEquals('PIM Sample Report 2', $report->getName());
        $this->assertEquals(3, $report->getReportGroup()->getId());
        $this->assertEquals(true, $report->isUseFilterField());
        $this->assertEquals('PIM_DEFINED', $report->getType());
        $this->assertEquals(2, $report->getId());
    }
}

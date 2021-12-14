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
use OrangeHRM\Entity\ReportingMethod;
use OrangeHRM\Pim\Dao\ReportingMethodConfigurationDao;
use OrangeHRM\Pim\Dto\ReportingMethodSearchFilterParams;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 */
class ReportingMethodConfigurationDaoTest extends TestCase
{
    private ReportingMethodConfigurationDao $reportingMethodConfigurationDao;
    protected string $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->reportingMethodConfigurationDao = new ReportingMethodConfigurationDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/ReportingMethodConfigurationDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testAddReportingMethod(): void
    {
        $reportingMethod = new ReportingMethod();
        $reportingMethod->setName('Finance');

        $this->reportingMethodConfigurationDao->saveReportingMethod($reportingMethod);

        $savedReportingMethod = TestDataService::fetchLastInsertedRecord('ReportingMethod', 'a.id');

        $this->assertTrue($savedReportingMethod instanceof ReportingMethod);
        $this->assertEquals('Finance', $savedReportingMethod->getName());
    }

    public function testEditReportingMethod(): void
    {
        $reportingMethod = TestDataService::fetchObject('ReportingMethod', 3);
        $reportingMethod->setName('Finance HR');

        $this->reportingMethodConfigurationDao->saveReportingMethod($reportingMethod);

        $savedReportingMethod = TestDataService::fetchLastInsertedRecord('ReportingMethod', 'a.id');

        $this->assertTrue($savedReportingMethod instanceof ReportingMethod);
        $this->assertEquals('Finance HR', $savedReportingMethod->getName());
    }

    public function testGetReportingMethodById(): void
    {
        $reportingMethod = $this->reportingMethodConfigurationDao->getReportingMethodById(1);

        $this->assertTrue($reportingMethod instanceof ReportingMethod);
        $this->assertEquals('Indirect', $reportingMethod->getName());
    }

    public function testGetReportingMethodList(): void
    {
        $reportingMethodFilterParams = new ReportingMethodSearchFilterParams();
        $result = $this->reportingMethodConfigurationDao->getReportingMethodList($reportingMethodFilterParams);
        $this->assertCount(3, $result);
        $this->assertTrue($result[0] instanceof ReportingMethod);
    }

    public function testGetReportingMethodListWithLimit(): void
    {
        $reportingMethodFilterParams = new ReportingMethodSearchFilterParams();
        $reportingMethodFilterParams->setLimit(2);

        $result = $this->reportingMethodConfigurationDao->getReportingMethodList($reportingMethodFilterParams);
        $this->assertCount(2, $result);
    }

    public function testGetReportingMethodCount(): void
    {
        $reportingMethodFilterParams = new ReportingMethodSearchFilterParams();

        $result = $this->reportingMethodConfigurationDao->getReportingMethodCount($reportingMethodFilterParams);
        $this->assertEquals(3, $result);
    }

    public function testDeleteReportingMethods(): void
    {
        $result = $this->reportingMethodConfigurationDao->deleteReportingMethods([1, 2]);
        $this->assertEquals(2, $result);
    }

    public function testDeleteWrongRecord(): void
    {
        $result = $this->reportingMethodConfigurationDao->deleteReportingMethods([4]);
        $this->assertEquals(0, $result);
    }

    public function testIsExistingReportingMethodName(): void
    {
        $this->assertTrue($this->reportingMethodConfigurationDao->isExistingReportingMethodName('Indirect'));
        $this->assertTrue($this->reportingMethodConfigurationDao->isExistingReportingMethodName('INDIRECT'));
        $this->assertTrue($this->reportingMethodConfigurationDao->isExistingReportingMethodName('indirect'));
        $this->assertTrue($this->reportingMethodConfigurationDao->isExistingReportingMethodName('  Indirect  '));
    }

    public function testGetReportingMethodByName(): void
    {
        $object = $this->reportingMethodConfigurationDao->getReportingMethodByName('Indirect');
        $this->assertTrue($object instanceof ReportingMethod);
        $this->assertEquals(1, $object->getId());

        $object = $this->reportingMethodConfigurationDao->getReportingMethodByName('INDIRECT');
        $this->assertTrue($object instanceof ReportingMethod);
        $this->assertEquals(1, $object->getId());

        $object = $this->reportingMethodConfigurationDao->getReportingMethodByName('indirect');
        $this->assertTrue($object instanceof ReportingMethod);
        $this->assertEquals(1, $object->getId());

        $object = $this->reportingMethodConfigurationDao->getReportingMethodByName('  Indirect  ');
        $this->assertTrue($object instanceof ReportingMethod);
        $this->assertEquals(1, $object->getId());

        $object = $this->reportingMethodConfigurationDao->getReportingMethodByName('Supervisor');
        $this->assertFalse($object instanceof ReportingMethod);
    }
}

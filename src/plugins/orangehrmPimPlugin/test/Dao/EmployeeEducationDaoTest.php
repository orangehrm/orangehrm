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

use DateTime;
use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\EmployeeEducation;
use OrangeHRM\Pim\Dao\EmployeeEducationDao;
use OrangeHRM\Pim\Dto\EmployeeEducationSearchFilterParams;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Dao
 */
class EmployeeEducationDaoTest extends TestCase
{
    private EmployeeEducationDao $employeeEducationDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->employeeEducationDao = new EmployeeEducationDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/EmployeeEducationDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmployeeEducationById(): void
    {
        $result = $this->employeeEducationDao->getEmployeeEducationById(1, 1);
        $this->assertEquals('UoM', $result->getInstitute());
        $this->assertEquals('CSE', $result->getMajor());
        $this->assertEquals('First Class', $result->getScore());
        $this->assertEquals(2020, $result->getYear());
    }

    public function testDeleteEmployeeEducation(): void
    {
        $toTobedeletedIds = [1, 2];
        $result = $this->employeeEducationDao->deleteEmployeeEducations(1, $toTobedeletedIds);
        $this->assertEquals(2, $result);
    }

    public function testSearchEmployeeEducation(): void
    {
        $employeeEducationSearchParams = new EmployeeEducationSearchFilterParams();
        $employeeEducationSearchParams->setEmpNumber(1);
        $result = $this->employeeEducationDao->searchEmployeeEducation($employeeEducationSearchParams);
        $this->assertCount(2, $result);
        $this->assertTrue($result[0] instanceof EmployeeEducation);
    }

    public function testSearchEmployeeEducationWithLimit(): void
    {
        $employeeEducationSearchParams = new EmployeeEducationSearchFilterParams();
        $employeeEducationSearchParams->setEmpNumber(1);
        $employeeEducationSearchParams->setLimit(1);

        $result = $this->employeeEducationDao->searchEmployeeEducation($employeeEducationSearchParams);
        $this->assertCount(1, $result);
    }

    public function testSaveEmployeeEducation(): void
    {
        $employeeEducation = new EmployeeEducation();
        $employeeEducation->getDecorator()->setEducationByEducationId(1);
        $employeeEducation->getDecorator()->setEmployeeByEmpNumber(3);
        $employeeEducation->setInstitute('UCSC');
        $employeeEducation->setMajor('CS');
        $employeeEducation->setYear(2020);
        $employeeEducation->setScore('First Class');
        $employeeEducation->setStartDate(new DateTime('2017-01-01'));
        $employeeEducation->setEndDate(new DateTime('2020-12-31'));
        $result = $this->employeeEducationDao->saveEmployeeEducation($employeeEducation);
        $this->assertTrue($result instanceof EmployeeEducation);
        $this->assertEquals("UCSC", $result->getInstitute());
        $this->assertEquals("CS", $result->getMajor());
        $this->assertEquals("First Class", $result->getScore());
        $this->assertEquals(2020, $result->getYear());
        $this->assertEquals(new DateTime("2017-01-01"), $result->getStartDate());
        $this->assertEquals(new DateTime("2020-12-31"), $result->getEndDate());
    }

    public function testEditEmployeeEducation(): void
    {
        $employeeEducation = $this->employeeEducationDao->getEmployeeEducationById(1, 1);
        $employeeEducation->setInstitute('UCSC');
        $employeeEducation->setMajor('CS');
        $employeeEducation->setYear(2020);
        $employeeEducation->setScore('First Class');
        $employeeEducation->setStartDate(new DateTime('2017-01-01'));
        $employeeEducation->setEndDate(new DateTime('2020-12-31'));
        $result = $this->employeeEducationDao->saveEmployeeEducation($employeeEducation);
        $this->assertTrue($result instanceof EmployeeEducation);
        $this->assertEquals("UCSC", $result->getInstitute());
        $this->assertEquals("CS", $result->getMajor());
        $this->assertEquals("First Class", $result->getScore());
        $this->assertEquals(2020, $result->getYear());
        $this->assertEquals(new DateTime("2017-01-01"), $result->getStartDate());
        $this->assertEquals(new DateTime("2020-12-31"), $result->getEndDate());
    }

    public function testGetSearchEmployeeEducationsCount(): void
    {
        $employeeEducationSearchParams = new EmployeeEducationSearchFilterParams();
        $employeeEducationSearchParams->setEmpNumber(1);
        $result = $this->employeeEducationDao->getSearchEmployeeEducationsCount($employeeEducationSearchParams);
        $this->assertEquals(2, $result);
    }
}

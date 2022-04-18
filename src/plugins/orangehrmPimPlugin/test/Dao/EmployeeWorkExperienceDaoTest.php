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
use OrangeHRM\Entity\EmpWorkExperience;
use OrangeHRM\Pim\Dao\EmployeeWorkExperienceDao;
use OrangeHRM\Pim\Dto\EmployeeWorkExperienceSearchFilterParams;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Dao
 */
class EmployeeWorkExperienceDaoTest extends TestCase
{
    private EmployeeWorkExperienceDao $employeeWorkExperienceDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->employeeWorkExperienceDao = new EmployeeWorkExperienceDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/EmployeeWorkExperienceDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmployeeWorkExperienceById(): void
    {
        $result = $this->employeeWorkExperienceDao->getEmployeeWorkExperienceById(1, 1);
        $this->assertEquals('SE', $result->getJobTitle());
        $this->assertEquals('OrangeHRM', $result->getEmployer());
        $this->assertEquals('Worked Hard', $result->getComments());
        $this->assertEquals('2', $result->getInternal());
        $this->assertEquals(new DateTime('2005-04-03'), $result->getFromDate());
        $this->assertEquals(new DateTime('2010-04-04'), $result->getToDate());
    }

    public function testDeleteEmployeeWorkExperience(): void
    {
        $toTobedeletedIds = [1, 2];
        $result = $this->employeeWorkExperienceDao->deleteEmployeeWorkExperiences(1, $toTobedeletedIds);
        $this->assertEquals(2, $result);
    }

    public function testSearchEmployeeWorkExperience(): void
    {
        $employeeWorkExperienceSearchParams = new EmployeeWorkExperienceSearchFilterParams();
        $employeeWorkExperienceSearchParams->setEmpNumber(1);
        $result = $this->employeeWorkExperienceDao->searchEmployeeWorkExperience($employeeWorkExperienceSearchParams);
        $this->assertCount(2, $result);
        $this->assertTrue($result[0] instanceof EmpWorkExperience);
    }

    public function testSearchEmployeeWorkExperienceWithLimit(): void
    {
        $employeeWorkExperienceSearchParams = new EmployeeWorkExperienceSearchFilterParams();
        $employeeWorkExperienceSearchParams->setEmpNumber(1);
        $employeeWorkExperienceSearchParams->setLimit(1);

        $result = $this->employeeWorkExperienceDao->searchEmployeeWorkExperience($employeeWorkExperienceSearchParams);
        $this->assertCount(1, $result);
    }

    public function testSaveEmployeeWorkExperience(): void
    {
        $employeeWorkExperience = new EmpWorkExperience();
        $employeeWorkExperience->getDecorator()->setEmployeeByEmpNumber(3);
        $employeeWorkExperience->setEmployer('OHRM');
        $employeeWorkExperience->setJobTitle('SE');
        $employeeWorkExperience->setComments('test');
        $employeeWorkExperience->setInternal(3);
        $employeeWorkExperience->setFromDate(new DateTime('2017-01-01'));
        $employeeWorkExperience->setToDate(new DateTime('2020-12-31'));
        $result = $this->employeeWorkExperienceDao->saveEmployeeWorkExperience($employeeWorkExperience);
        $this->assertTrue($result instanceof EmpWorkExperience);
        $this->assertEquals("OHRM", $result->getEmployer());
        $this->assertEquals("SE", $result->getJobTitle());
        $this->assertEquals("test", $result->getComments());
        $this->assertEquals(3, $result->getInternal());
        $this->assertEquals(new DateTime("2017-01-01"), $result->getFromDate());
        $this->assertEquals(new DateTime("2020-12-31"), $result->getToDate());
    }

    public function testEditEmployeeWorkExperience(): void
    {
        $employeeWorkExperience = $this->employeeWorkExperienceDao->getEmployeeWorkExperienceById(1, 1);
        $employeeWorkExperience->setEmployer('OHRM');
        $employeeWorkExperience->setJobTitle('SE');
        $employeeWorkExperience->setComments('test');
        $employeeWorkExperience->setInternal(3);
        $employeeWorkExperience->setFromDate(new DateTime('2017-01-01'));
        $employeeWorkExperience->setToDate(new DateTime('2020-12-31'));
        $result = $this->employeeWorkExperienceDao->saveEmployeeWorkExperience($employeeWorkExperience);
        $this->assertTrue($result instanceof EmpWorkExperience);
        $this->assertEquals("OHRM", $result->getEmployer());
        $this->assertEquals("SE", $result->getJobTitle());
        $this->assertEquals("test", $result->getComments());
        $this->assertEquals(3, $result->getInternal());
        $this->assertEquals(new DateTime("2017-01-01"), $result->getFromDate());
        $this->assertEquals(new DateTime("2020-12-31"), $result->getToDate());
    }

    public function testGetSearchEmployeeWorkExperiencesCount(): void
    {
        $employeeWorkExperienceSearchParams = new EmployeeWorkExperienceSearchFilterParams();
        $employeeWorkExperienceSearchParams->setEmpNumber(1);
        $result = $this->employeeWorkExperienceDao->getSearchEmployeeWorkExperiencesCount($employeeWorkExperienceSearchParams);
        $this->assertEquals(2, $result);
    }
}

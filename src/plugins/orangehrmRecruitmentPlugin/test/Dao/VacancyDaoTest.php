<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Tests\Recruitment\Dao;

use DateTime;
use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Vacancy;
use OrangeHRM\Recruitment\Dao\VacancyDao;
use OrangeHRM\Recruitment\Dto\VacancySearchFilterParams;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Recruitment
 * @group Dao
 */
class VacancyDaoTest extends KernelTestCase
{
    private VacancyDao $vacancyDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->vacancyDao = new VacancyDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmRecruitmentPlugin/test/fixtures/JobVacancyDao.yaml';
        TestDataService::populate($this->fixture);
    }

    public function testSaveVacancy(): void
    {
        $vacancy = new Vacancy();
        $vacancy->setName('Tesla Battery Specialist');
        $vacancy->getDecorator()->setJobTitleById(4);
        $vacancy->getDecorator()->setEmployeeById(2);
        $vacancy->setNumOfPositions(3);
        $vacancy->setStatus(1);
        $vacancy->setDescription('This vacancy is for highly skilled electronic engineers');
        $vacancy->setDefinedTime(new DateTime('2022-01-01 10:45'));
        $vacancy->setUpdatedTime(new DateTime('2022-01-01 10:45'));
        $result = $this->vacancyDao->saveJobVacancy($vacancy);
        $this->assertInstanceOf(Vacancy::class, $result);
        $this->assertEquals('Tesla Battery Specialist', $result->getName());
        $this->assertEquals(new DateTime('2022-01-01 10:45'), $result->getDefinedTime());
    }

    public function testGetVacancyById(): void
    {
        $vacancy = $this->vacancyDao->getVacancyById(2);
        $this->assertInstanceOf(Vacancy::class, $vacancy);
        $this->assertEquals('Senior Technical Supervisor', $vacancy->getName());
        $this->assertEquals('3', $vacancy->getNumOfPositions());
    }

    public function testUpdateVacancy(): void
    {
        $vacancy = $this->vacancyDao->getVacancyById(1);
        $vacancy->setName('Technical Officer');
        $vacancy->setNumOfPositions(5);
        $vacancy->setUpdatedTime(new DateTime('2020-10-09 12:45'));
        $result = $this->vacancyDao->saveJobVacancy($vacancy);
        $this->assertEquals('Technical Officer', $result->getName());
        $this->assertEquals(5, $result->getNumOfPositions());
        $this->assertEquals('Assists the engineers', $result->getDescription());
        $this->assertEquals(new DateTime('2020-10-09 12:45'), $result->getUpdatedTime());
    }

    public function testDeleteVacancies(): void
    {
        $result = $this->vacancyDao->deleteVacancies([1, 2]);
        $this->assertTrue($result);
        $vacancy = $this->vacancyDao->getVacancyById(1);
        $this->assertNull($vacancy);
    }

    public function testGetVacancies(): void
    {
        $vacancyParamHolder = new VacancySearchFilterParams();
        $vacancies = $this->vacancyDao->getVacancies($vacancyParamHolder);
        $this->assertCount(6, $vacancies);
        $this->assertEquals('Assistant Technical Supervisor', $vacancies[0]->getName());
    }

    public function testGetVacanciesFilterByJobTitle(): void
    {
        $vacancyParamHolder = new VacancySearchFilterParams();
        $vacancyParamHolder->setJobTitleId(2);
        $vacancies = $this->vacancyDao->getVacancies($vacancyParamHolder);
        $this->assertCount(2, $vacancies);
        $this->assertEquals('Senior Technical Supervisor', $vacancies[1]->getName());
    }

    public function testGetVacanciesFilterByVacancyId(): void
    {
        $vacancyParamHolder = new VacancySearchFilterParams();
        $vacancyParamHolder->setVacancyIds([2]);
        $vacancies = $this->vacancyDao->getVacancies($vacancyParamHolder);
        $this->assertCount(1, $vacancies);
        $this->assertEquals('Senior Technical Supervisor', $vacancies[0]->getName());
    }

    public function testGetVacanciesFilterByHiringMangerId(): void
    {
        $vacancyParamHolder = new VacancySearchFilterParams();
        $vacancyParamHolder->setEmpNumber(1);
        $vacancies = $this->vacancyDao->getVacancies($vacancyParamHolder);
        $this->assertCount(3, $vacancies);
        $this->assertEquals('Electrical Engineer Officer', $vacancies[0]->getName());
    }

    public function testGetVacanciesFilterByStatus(): void
    {
        $vacancyParamHolder = new VacancySearchFilterParams();
        $vacancyParamHolder->setStatus(false);
        $vacancies = $this->vacancyDao->getVacancies($vacancyParamHolder);
        $this->assertCount(1, $vacancies);
        $this->assertEquals('Part-Time Technical Assistant', $vacancies[0]->getName());
    }

    public function testGetVacancyListGroupByHiringManager(): void
    {
        $vacancySearchFilterParams = new VacancySearchFilterParams();
        $vacancySearchFilterParams->setVacancyIds([1,2,3,4]);
        $vacancies = $this->vacancyDao->getHiringManagerEmpNumberList($vacancySearchFilterParams);
        $this->assertCount(3, $vacancies);
    }

    public function testSearchVacanciesCount(): void
    {
        $vacancyParamHolder = new VacancySearchFilterParams();
        $vacanciesCount = $this->vacancyDao->getVacanciesCount($vacancyParamHolder);
        $this->assertEquals(6, $vacanciesCount);
    }
}

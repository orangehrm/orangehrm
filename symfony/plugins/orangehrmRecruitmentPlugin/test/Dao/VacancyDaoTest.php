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

namespace OrangeHRM\Recruitment\Tests\Dao;

use DateTime;
use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Vacancy;
use OrangeHRM\Recruitment\Dao\VacancyDao;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

class VacancyDaoTest extends TestCase
{
    private VacancyDao $vacancyDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp():void {

        $this->vacancyDao = new VacancyDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmRecruitmentPlugin/test/fixtures/CandidateDao.yml';
        TestDataService::populate($this->fixture);
    }


    /**
     * Testing getVacancyList
     * @throws DaoException
     */
    public function testSaveVacancy() {
        TestDataService::truncateSpecificTables(array('Vacancy'));

        $Vacancy = new Vacancy();
        $Vacancy->getDecorator()->setJobTitleById(1);
        $Vacancy->getDecorator()->setEmployeeById(1);
        $Vacancy->setName("QA Engineer");
        $Vacancy->getDecorator()->setIsPublished(true);
        $Vacancy->setNumOfPositions(2);
        $Vacancy->setDescription("test");
        $Vacancy->setStatus(1);
        $Vacancy->setDefinedTime(new DateTime());
        $Vacancy->setUpdatedTime(new DateTime());
        $result = $this->vacancyDao->saveJobVacancy($Vacancy);
        $this->assertTrue($result);
    }

    /**
     * Testing deleteVacancies true arguments
     */
//    public function testDeleteVacanciesForTrue() {
//
//        $vacancyIds = array(1, 3);
//        $result = $this->vacancyDao->deleteVacancies($vacancyIds);
//        $this->assertEquals(true, $result);
//
//        $vacancyIds = array(2);
//        $result = $this->vacancyDao->deleteVacancies($vacancyIds);
//        $this->assertEquals(true, $result);
//    }

//
//    /**
//     * Testing getAllVacancies
//     * @throws DaoException
//     */
//    public function testGetAllVacancies() {
//
//        $vacancyList = $this->vacancyDao->getAllVacancies();
//        $this->assertTrue($vacancyList[0] instanceof Vacancy);
//    }


//    public function testGetVacancyById() {
//        $vacancyId = 1;
//        $result = $this->vacancyDao->getVacancyById($vacancyId);
//        $this->assertTrue($result instanceof Vacancy);
//        $this->assertEquals(1, $result->getJobTitle());
//        $this->assertEquals(2, $result->getNumOfPositions());
//        $this->assertEquals(1, $result->getStatus());
//    }

}
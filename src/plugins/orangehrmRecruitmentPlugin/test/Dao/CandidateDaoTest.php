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
use OrangeHRM\Entity\Candidate;
use OrangeHRM\Entity\CandidateHistory;
use OrangeHRM\Entity\CandidateVacancy;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Vacancy;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Recruitment\Dao\CandidateDao;
use OrangeHRM\Recruitment\Dto\CandidateSearchFilterParams;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Recruitment
 * @group Dao
 */
class CandidateDaoTest extends KernelTestCase
{
    private CandidateDao $candidateDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->candidateDao = new CandidateDao();
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmRecruitmentPlugin/test/fixtures/JobCandidateDao.yaml';
        TestDataService::populate($this->fixture);
    }

    public function testSaveCandidate(): void
    {
        $candidate = new Candidate();
        $candidate->setFirstName('Sandeepa');
        $candidate->setMiddleName('RA');
        $candidate->setLastName('Ranathunga');
        $candidate->getDecorator()->setAddedPersonById(1);
        $candidate->setComment('Candidate Initiated');
        $candidate->setEmail('sandeepa@valkrie.com');
        $candidate->setStatus(1);
        $candidate->setDateOfApplication(new DateTime('2022-05-25 08:15'));
        $candidate->setContactNumber('0778084747');
        $candidate->setConsentToKeepData(true);
        $candidate->setModeOfApplication(1);
        $candidate->setKeywords('Spring-boot,Symfony,node.js');
        $result = $this->candidateDao->saveCandidate($candidate);
        $this->assertInstanceOf(Candidate::class, $result);
        $this->assertInstanceOf(Employee::class, $result->getAddedPerson());
        $this->assertEquals('sandeepa@valkrie.com', $result->getEmail());
    }

    public function testSaveCandidateVacancy(): void
    {
        $candidateVacancy = new CandidateVacancy();
        $candidateVacancy->getDecorator()->setCandidateById(4);
        $candidateVacancy->getDecorator()->setVacancyById(1);
        $candidateVacancy->setAppliedDate(new DateTime('2022-05-26 16:24'));
        $candidateVacancy->setStatus('APPLICATION INITIATED');
        $result = $this->candidateDao->saveCandidateVacancy($candidateVacancy);
        $this->assertInstanceOf(CandidateVacancy::class, $result);
        $this->assertInstanceOf(Candidate::class, $result->getCandidate());
        $this->assertInstanceOf(Vacancy::class, $result->getVacancy());
        $this->assertEquals('Technical Assistant Intern', $result->getVacancy()->getName());
        $this->assertEquals('Smith', $result->getCandidate()->getLastName());
    }

    public function testGetCandidateById(): void
    {
        $candidate = $this->candidateDao->getCandidateById(1);
        $this->assertInstanceOf(Candidate::class, $candidate);
        $this->assertEquals('Peter', $candidate->getFirstName());
        $this->assertEquals('petersmith@gmail.com', $candidate->getEmail());
        $this->assertEquals(new DateTime('2020-10-07'), $candidate->getDateOfApplication());

        $candidate = $this->candidateDao->getCandidateById(100);
        $this->assertNull($candidate);
    }

    public function testGetCandidateVacancyByCandidateId(): void
    {
        $candidateVacancy = $this->candidateDao->getCandidateVacancyByCandidateId(1);
        $this->assertInstanceOf(CandidateVacancy::class, $candidateVacancy);
        $this->assertInstanceOf(Candidate::class, $candidateVacancy->getCandidate());
        $this->assertInstanceOf(Vacancy::class, $candidateVacancy->getVacancy());
        $this->assertEquals('APPLICATION INITIATED', $candidateVacancy->getStatus());
        $this->assertEquals('Technical Assistant Intern', $candidateVacancy->getVacancy()->getName());
        $this->assertEquals('Peter', $candidateVacancy->getCandidate()->getFirstName());

        $candidateVacancy = $this->candidateDao->getCandidateVacancyByCandidateId(100);
        $this->assertNull($candidateVacancy);
    }

    public function testDeleteCandidates(): void
    {
        $result = $this->candidateDao->deleteCandidates([1, 2]);
        $this->assertTrue($result);
        $candidate = $this->candidateDao->getCandidateById(1);
        $this->assertNull($candidate);
    }

    public function testDeleteCandidateVacancy(): void
    {
        $result = $this->candidateDao->deleteCandidateVacancy(1);
        $this->assertTrue($result);
        $candidateVacancy = $this->candidateDao->getCandidateVacancyByCandidateId(1);
        $this->assertNull($candidateVacancy);
    }

    public function testGetCandidateList(): void
    {
        $candidateSearchFilterParams = new CandidateSearchFilterParams();
        $candidateList = $this->candidateDao->getCandidatesList($candidateSearchFilterParams);
        $this->assertCount(5, $candidateList);
        $this->assertInstanceOf(Candidate::class, $candidateList[0]);
        $this->assertEquals('Charles', $candidateList[0]->getFirstName());
        $this->assertEquals('John', $candidateList[1]->getFirstName());
        $this->assertEquals('Jo', $candidateList[2]->getFirstName());
        $this->assertEquals('Richard', $candidateList[3]->getFirstName());
        $this->assertEquals('Peter', $candidateList[4]->getFirstName());
        $this->assertEquals(5, $candidateList[0]->getCandidateVacancy()[0]->getID());
    }

    public function testGetCandidateListFilterByCandidateId(): void
    {
        $candidateSearchFilterParams = new CandidateSearchFilterParams();
        $candidateSearchFilterParams->setCandidateId(1);
        $candidateList = $this->candidateDao->getCandidatesList($candidateSearchFilterParams);
        $this->assertCount(1, $candidateList);
        $this->assertInstanceOf(Candidate::class, $candidateList[0]);
        $this->assertEquals('Peter', $candidateList[0]->getFirstName());
        $this->assertEquals(1, $candidateList[0]->getCandidateVacancy()[0]->getID());
    }

    public function testGetCandidateListFilterByVacancyId(): void
    {
        $candidateSearchFilterParams = new CandidateSearchFilterParams();
        $candidateSearchFilterParams->setVacancyId(2);
        $candidateList = $this->candidateDao->getCandidatesList($candidateSearchFilterParams);
        $this->assertCount(2, $candidateList);
        $this->assertInstanceOf(Candidate::class, $candidateList[0]);
        $this->assertEquals('Charles', $candidateList[0]->getFirstName());
        $this->assertEquals('Richard', $candidateList[1]->getFirstName());
        $this->assertEquals(5, $candidateList[0]->getCandidateVacancy()[0]->getID());
    }

    public function testGetCandidateListFilterByJobTitleId(): void
    {
        $candidateSearchFilterParams = new CandidateSearchFilterParams();
        $candidateSearchFilterParams->setJobTitleId(1);
        $candidateList = $this->candidateDao->getCandidatesList($candidateSearchFilterParams);
        $this->assertCount(2, $candidateList);
        $this->assertInstanceOf(Candidate::class, $candidateList[0]);
        $this->assertEquals('Jo', $candidateList[0]->getFirstName());
        $this->assertEquals('Peter', $candidateList[1]->getFirstName());
        $this->assertEquals(2, $candidateList[0]->getCandidateVacancy()[0]->getID());
    }

    public function testGetCandidateListFilterByHiringMangerId(): void
    {
        $candidateSearchFilterParams = new CandidateSearchFilterParams();
        $candidateSearchFilterParams->setHiringManagerId(1);
        $candidateList = $this->candidateDao->getCandidatesList($candidateSearchFilterParams);
        $this->assertCount(2, $candidateList);
        $this->assertInstanceOf(Candidate::class, $candidateList[0]);
        $this->assertEquals('Jo', $candidateList[0]->getFirstName());
        $this->assertEquals('Peter', $candidateList[1]->getFirstName());
        $this->assertEquals(2, $candidateList[0]->getCandidateVacancy()[0]->getID());
    }

    public function testGetCandidateListFilterByStatus(): void
    {
        $candidateSearchFilterParams = new CandidateSearchFilterParams();
        $candidateSearchFilterParams->setStatus('SHORTLISTED');
        $candidateList = $this->candidateDao->getCandidatesList($candidateSearchFilterParams);
        $this->assertCount(2, $candidateList);
        $this->assertInstanceOf(Candidate::class, $candidateList[0]);
        $this->assertEquals('Charles', $candidateList[0]->getFirstName());
        $this->assertEquals('Jo', $candidateList[1]->getFirstName());
        $this->assertEquals(5, $candidateList[0]->getCandidateVacancy()[0]->getID());
    }

    public function testGetCandidateListFilterByKeywords(): void
    {
        $candidateSearchFilterParams = new CandidateSearchFilterParams();
        $candidateSearchFilterParams->setKeywords('Spring-boot');
        $candidateList = $this->candidateDao->getCandidatesList($candidateSearchFilterParams);
        $this->assertCount(1, $candidateList);
        $this->assertInstanceOf(Candidate::class, $candidateList[0]);
        $this->assertEquals('John', $candidateList[0]->getFirstName());
        $this->assertNull($candidateList[0]->getCandidateVacancy()[0]);
    }

    public function testGetCandidateListFilterByMethodOfApplication(): void
    {
        $candidateSearchFilterParams = new CandidateSearchFilterParams();
        $candidateSearchFilterParams->setMethodOfApplication(2);
        $candidateList = $this->candidateDao->getCandidatesList($candidateSearchFilterParams);
        $this->assertCount(2, $candidateList);
        $this->assertInstanceOf(Candidate::class, $candidateList[0]);
        $this->assertEquals('John', $candidateList[0]->getFirstName());
        $this->assertEquals('Jo', $candidateList[1]->getFirstName());
        $this->assertNull($candidateList[0]->getCandidateVacancy()[0]);
    }

    public function testSaveCandidateHistory(): void
    {
        $candidateVacancy = $this->candidateDao->getCandidateVacancyByCandidateId(1);
        $candidateHistory = new CandidateHistory();
        $candidateHistory->getDecorator()->setCandidateById(1);
        $candidateHistory->getDecorator()->setVacancyById($candidateVacancy->getVacancy()->getId());
        $candidateHistory->setCandidateVacancyName($candidateVacancy->getVacancy()->getName());
        $candidateHistory->setAction(WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_REJECT);
        $candidateHistory->getDecorator()->setPerformedBy(1);
        $candidateHistory->setPerformedDate(new DateTime('2022-06-01'));
        $candidateHistory->setNote('Rejected effect from 2022-06-01');
        $result = $this->candidateDao->saveCandidateHistory($candidateHistory);
        $this->assertInstanceOf(CandidateHistory::class, $result);
        $this->assertInstanceOf(Vacancy::class, $result->getVacancy());
        $this->assertEquals('Rejected effect from 2022-06-01', $result->getNote());
        $this->assertEquals(3, $result->getAction());
    }
}

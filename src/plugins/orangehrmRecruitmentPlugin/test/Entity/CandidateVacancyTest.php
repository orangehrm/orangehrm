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

namespace OrangeHRM\Tests\Recruitment\Entity;

use DateTime;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Candidate;
use OrangeHRM\Entity\CandidateVacancy;
use OrangeHRM\Entity\Vacancy;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Recruitment
 * @group Entity
 */
class CandidateVacancyTest extends EntityTestCase
{
    use EntityManagerHelperTrait;

    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([CandidateVacancy::class]);
        $fixture = Config::get(Config::PLUGINS_DIR)
            .'/orangehrmRecruitmentPlugin/test/fixtures/CandidateVacancy.yml';
        TestDataService::populate($fixture);
    }

    public function testCandidateVacancyEntity(): void
    {
        $candidateVacancy = new CandidateVacancy();
        $candidateVacancy->getDecorator()->setVacancyById(1);
        $candidateVacancy->getDecorator()->setCandidateById(1);
        $candidateVacancy->setAppliedDate(new DateTime('2022-05-25 08:37'));
        $candidateVacancy->setStatus('APPLICATION INITIATED');
        $this->persist($candidateVacancy);
        $candidateVacancy = $this->createQueryBuilder(CandidateVacancy::class, 'candidateVacancy')
            ->where('candidateVacancy.candidate = :candidateId')
            ->setParameter('candidateId', 1)
            ->getQuery()
            ->getOneOrNullResult();
        $this->assertInstanceOf(CandidateVacancy::class, $candidateVacancy);
        $this->assertInstanceOf(Candidate::class, $candidateVacancy->getCandidate());
        $this->assertInstanceOf(Vacancy::class, $candidateVacancy->getVacancy());
        $this->assertEquals('APPLICATION INITIATED', $candidateVacancy->getStatus());
        $this->assertEquals(new DateTime('2022-05-25 08:37'), $candidateVacancy->getAppliedDate());
    }
}

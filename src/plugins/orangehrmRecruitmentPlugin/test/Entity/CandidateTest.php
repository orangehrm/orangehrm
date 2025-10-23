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
use OrangeHRM\Entity\Candidate;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Recruitment
 * @group Entity
 */
class CandidateTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([Candidate::class]);
    }

    public function testCandidateEntity(): void
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

        $this->persist($candidate);
        $candidate = $this->getRepository(Candidate::class)->find(1);
        $this->assertInstanceOf(Candidate::class, $candidate);
        $this->assertInstanceOf(Employee::class, $candidate->getAddedPerson());
        $this->assertEquals('sandeepa@valkrie.com', $candidate->getEmail());
    }
}

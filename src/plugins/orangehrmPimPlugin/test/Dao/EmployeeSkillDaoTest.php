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

use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\EmployeeSkill;
use OrangeHRM\Entity\Skill;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\Pim\Dao\EmployeeSkillDao;
use OrangeHRM\Pim\Dto\EmployeeSkillSearchFilterParams;
use OrangeHRM\Pim\Dto\EmployeeAllowedSkillSearchFilterParams;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Dao
 */
class EmployeeSkillDaoTest extends TestCase
{
    private EmployeeSkillDao $employeeSkillDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->employeeSkillDao = new EmployeeSkillDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/EmployeeSkillDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmployeeSkillById(): void
    {
        $result = $this->employeeSkillDao->getEmployeeSkillById(1, 1);
        $this->assertEquals('comment 1', $result->getComments());
        $this->assertEquals(1, $result->getYearsOfExp());
    }

    public function testDeleteEmployeeSkill(): void
    {
        $toTobedeletedIds = [1, 2];
        $result = $this->employeeSkillDao->deleteEmployeeSkills(1, $toTobedeletedIds);
        $this->assertEquals(2, $result);
    }

    public function testSearchEmployeeSkill(): void
    {
        $employeeSkillSearchParams = new EmployeeSkillSearchFilterParams();
        $employeeSkillSearchParams->setEmpNumber(1);
        $result = $this->employeeSkillDao->searchEmployeeSkill($employeeSkillSearchParams);
        $this->assertCount(2, $result);
        $this->assertTrue($result[0] instanceof EmployeeSkill);
    }

    public function testSearchEmployeeSkillWithLimit(): void
    {
        $employeeSkillSearchParams = new EmployeeSkillSearchFilterParams();
        $employeeSkillSearchParams->setEmpNumber(1);
        $employeeSkillSearchParams->setLimit(1);

        $result = $this->employeeSkillDao->searchEmployeeSkill($employeeSkillSearchParams);
        $this->assertCount(1, $result);
    }

    public function testSaveEmployeeSkill(): void
    {
        $employeeSkill = new EmployeeSkill();
        $employeeSkill->getDecorator()->setSkillBySkillId(1);
        $employeeSkill->getDecorator()->setEmployeeByEmpNumber(3);
        $employeeSkill->setComments('comment 5');
        $employeeSkill->setYearsOfExp(4);
        $result = $this->employeeSkillDao->saveEmployeeSkill($employeeSkill);
        $this->assertTrue($result instanceof EmployeeSkill);
        $this->assertEquals("comment 5", $result->getComments());
        $this->assertEquals(4, $result->getYearsOfExp());
    }

    public function testEditEmployeeSkill(): void
    {
        $employeeSkill = $this->employeeSkillDao->getEmployeeSkillById(1, 1);
        $employeeSkill->setComments("changed comment");
        $employeeSkill->setYearsOfExp(10);
        $result = $this->employeeSkillDao->saveEmployeeSkill($employeeSkill);
        $this->assertTrue($result instanceof EmployeeSkill);
        $this->assertEquals("changed comment", $result->getComments());
        $this->assertEquals(10, $result->getYearsOfExp());
    }

    public function testGetSearchEmployeeSkillsCount(): void
    {
        $employeeSkillSearchParams = new EmployeeSkillSearchFilterParams();
        $employeeSkillSearchParams->setEmpNumber(1);
        $result = $this->employeeSkillDao->getSearchEmployeeSkillsCount($employeeSkillSearchParams);
        $this->assertEquals(2, $result);
    }

    public function testGetEmployeeAllowedSkills(): void
    {
        $skillSearchFilterParams = new EmployeeAllowedSkillSearchFilterParams();
        $skillSearchFilterParams->setEmpNumber(1);
        $skills = $this->employeeSkillDao->getEmployeeAllowedSkills($skillSearchFilterParams);
        $resultSkillIds = [];
        foreach ($skills as $skill) {
            $resultSkillIds[] = $skill->getId();
        }

        /** @var Skill[] $skills */
        $skills = $this->getEntityManager()->getRepository(Skill::class)->findBy([], ['name' => ListSorter::ASCENDING]);
        $skillIds = [];
        foreach ($skills as $skill) {
            $skillIds[] = $skill->getId();
        }
        /** @var EmployeeSkill[] $employeeSkills */
        $employeeSkills = $this->getEntityManager()->getRepository(EmployeeSkill::class)->findBy(['employee' => 1]);
        $employeeSkillIds = [];
        foreach ($employeeSkills as $employeeSkill) {
            $employeeSkillIds[] = $employeeSkill->getSkill()->getId();
        }

        $this->assertEquals(array_values(array_diff($skillIds, $employeeSkillIds)), $resultSkillIds);

        $skillSearchFilterParams = new EmployeeAllowedSkillSearchFilterParams();
        $skillSearchFilterParams->setEmpNumber(2);
        $skills = $this->employeeSkillDao->getEmployeeAllowedSkills($skillSearchFilterParams);
        $this->assertCount(3, $skills);
        $this->assertEquals(
            ['Drawing', 'Driving', 'Sign Language'],
            array_map(
                function (Skill $skill) {
                    return $skill->getName();
                },
                $skills
            )
        );

        $skillSearchFilterParams = new EmployeeAllowedSkillSearchFilterParams();
        $skillSearchFilterParams->setEmpNumber(100);
        $skills = $this->employeeSkillDao->getEmployeeAllowedSkills($skillSearchFilterParams);
        $this->assertCount(4, $skills);
        $this->assertEquals(
            ['Drawing', 'Driving', 'Sign Language', 'Skydiving'],
            array_map(
                function (Skill $skill) {
                    return $skill->getName();
                },
                $skills
            )
        );
    }

    public function testGetEmployeeAllowedSkillsCount(): void
    {
        $skillSearchFilterParams = new EmployeeAllowedSkillSearchFilterParams();
        $skillSearchFilterParams->setEmpNumber(1);
        $skillsCount = $this->employeeSkillDao->getEmployeeAllowedSkillsCount($skillSearchFilterParams);
        $this->assertEquals(2, $skillsCount);

        $skillSearchFilterParams = new EmployeeAllowedSkillSearchFilterParams();
        $skillSearchFilterParams->setEmpNumber(2);
        $skillsCount = $this->employeeSkillDao->getEmployeeAllowedSkillsCount($skillSearchFilterParams);
        $this->assertEquals(3, $skillsCount);

        $skillSearchFilterParams = new EmployeeAllowedSkillSearchFilterParams();
        $skillSearchFilterParams->setEmpNumber(100);
        $skillsCount = $this->employeeSkillDao->getEmployeeAllowedSkillsCount($skillSearchFilterParams);
        $this->assertEquals(4, $skillsCount);
    }
}

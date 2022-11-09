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

namespace OrangeHRM\Tests\Admin\Service;

use Exception;
use OrangeHRM\Admin\Dao\SkillDao;
use OrangeHRM\Admin\Dto\SkillSearchFilterParams;
use OrangeHRM\Admin\Service\SkillService;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Skill;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Service
 */
class SkillServiceTest extends TestCase
{
    private SkillService $skillService;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->skillService = new SkillService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/SkillDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testDeleteSkill(): void
    {
        $toBeDeletedSkillIds = [1, 2];

        $skillDao = $this->getMockBuilder(SkillDao::class)->getMock();

        $skillDao->expects($this->once())
            ->method('deleteSkills')
            ->with($toBeDeletedSkillIds)
            ->will($this->returnValue(2));

        $this->skillService->setSkillDao($skillDao);
        $result = $this->skillService->deleteSkills($toBeDeletedSkillIds);
        $this->assertEquals(2, $result);
    }

    public function testGetSkillById(): void
    {
        $skillList = TestDataService::loadObjectList('Skill', $this->fixture, 'Skill');
        $skillDao = $this->getMockBuilder(SkillDao::class)->getMock();

        $skillDao->expects($this->once())
            ->method('getSkillById')
            ->with(1)
            ->will($this->returnValue($skillList[0]));

        $this->skillService->setSkillDao($skillDao);
        $result = $this->skillService->getSkillById(1);
        $this->assertEquals($skillList[0], $result);
    }

    public function testSaveSkill(): void
    {
        $skill = new Skill();
        $skill->setName("Swimming");
        $skill->setDescription("Ability to swim");

        $skillDao = $this->getMockBuilder(SkillDao::class)->getMock();

        $skillDao->expects($this->once())
            ->method('saveSkill')
            ->with($skill)
            ->will($this->returnValue($skill));

        $this->skillService->setSkillDao($skillDao);
        $result = $this->skillService->saveSkill($skill);
        $this->assertEquals($skill, $result);
    }

    public function testSearchSkill(): void
    {
        $skillList = TestDataService::loadObjectList('Skill', $this->fixture, 'Skill');
        $skillSearchParams = new SkillSearchFilterParams();
        $skillDao = $this->getMockBuilder(SkillDao::class)->getMock();

        $skillDao->expects($this->once())
            ->method('searchSkill')
            ->with($skillSearchParams)
            ->will($this->returnValue($skillList));

        $this->skillService->setSkillDao($skillDao);
        $result = $this->skillService->searchSkill($skillSearchParams);
        $this->assertCount(3, $result);
        $this->assertTrue($result[0] instanceof Skill);
    }

    public function testGetSearchSkillsCount(): void
    {
        $skillSearchParams = new SkillSearchFilterParams();
        $skillDao = $this->getMockBuilder(SkillDao::class)->getMock();

        $skillDao->expects($this->once())
            ->method('getSearchSkillsCount')
            ->with($skillSearchParams)
            ->will($this->returnValue(3));

        $this->skillService->setSkillDao($skillDao);
        $result = $this->skillService->getSearchSkillsCount($skillSearchParams);
        $this->assertEquals(3, $result);
    }
}

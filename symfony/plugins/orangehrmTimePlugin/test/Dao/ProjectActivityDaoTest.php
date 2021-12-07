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

namespace OrangeHRM\Tests\Time\Dao;

use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\ProjectActivity;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;
use OrangeHRM\Time\Dao\ProjectActivityDao;
use OrangeHRM\Time\Dto\ProjectActivitySearchFilterParams;
use OrangeHRM\Time\Exception\ProjectServiceException;

/**
 * @group Time
 * @group Dao
 */
class ProjectActivityDaoTest extends TestCase
{
    /**
     * @var ProjectActivityDao
     */
    private ProjectActivityDao $projectActivityDao;

    /**
     * @var string
     */
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->projectActivityDao = new ProjectActivityDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmTimePlugin/test/fixtures/ProjectActivityDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetProjectActivityList(): void
    {
        $projectActivitySearchFilterParams = new ProjectActivitySearchFilterParams();
        $result = $this->projectActivityDao->getProjectActivitiesPaginator(1, $projectActivitySearchFilterParams);
        $this->assertCount(2, $result);
    }

    public function testGetProjectActivityById(): void
    {
        $projectActivity = $this->projectActivityDao->getProjectActivityByProjectIdAndProjectActivityId(1, 1);
        $this->assertEquals(1, $projectActivity->getId());
        $this->assertEquals(1, $projectActivity->getProject()->getId());
        $this->assertEquals('Activity1 For Pro1', $projectActivity->getName());
        $this->assertTrue($projectActivity instanceof ProjectActivity);
    }

    public function testSaveProjectActivity(): void
    {
        $projectActivity = new ProjectActivity();
        $projectActivity->setName("Debug");
        $projectActivity->getDecorator()->setProjectById(1);
        $result = $this->projectActivityDao->saveProjectActivity($projectActivity);

        $this->assertTrue($result instanceof ProjectActivity);
        $this->assertEquals('Debug', $projectActivity->getName());
    }

    public function testEditProjectActivity(): void
    {
        $projectActivity = $this->projectActivityDao->getProjectActivityByProjectIdAndProjectActivityId(1, 1);
        $projectActivity->setName("Test");
        $result = $this->projectActivityDao->saveProjectActivity($projectActivity);

        $this->assertTrue($result instanceof ProjectActivity);
        $this->assertEquals('Test', $projectActivity->getName());
    }

    public function testExceptionForDeleteProjectActivity(): void
    {
        try {
            $toTobedeletedIds = [1];
            $this->projectActivityDao->deleteProjectActivities($toTobedeletedIds);
            $this->fail('Exception expected');
        } catch (Exception $exception) {
            $this->assertTrue($exception instanceof ProjectServiceException);
        }
    }

    public function testDeleteProjectActivity(): void
    {
        $toTobedeletedIds = [2];
        $result = $this->projectActivityDao->deleteProjectActivities($toTobedeletedIds);
        $this->assertEquals(1, $result);
    }
}

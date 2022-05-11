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

namespace OrangeHRM\Tests\Performance\Dao;

use OrangeHRM\Config\Config;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Services;
use OrangeHRM\Performance\Dao\PerformanceReviewDao;
use OrangeHRM\Performance\Dto\ReviewListSearchFilterParams;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\Mock\MockAuthUser;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Performance
 * @group Dao
 */
class PerformanceReviewDaoTest extends KernelTestCase
{
    private PerformanceReviewDao $performanceReviewDao;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->performanceReviewDao = new PerformanceReviewDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPerformancePlugin/test/fixtures/PerformanceReviewDao.yaml';
        TestDataService::populate($this->fixture);

        $authUser = $this->getMockBuilder(MockAuthUser::class)
            ->onlyMethods(['getUserId', 'getEmpNumber', 'getUserRoleId'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->method('getUserId')
            ->willReturn(1);
        $authUser->method('getEmpNumber')
            ->willReturn(
                $this->getEntityReference(
                    User::class,
                    1
                )->getEmployee()->getEmpNumber()
            );
        $this->createKernelWithMockServices([Services::AUTH_USER => $authUser]);
    }

    public function testGetReviewList(): void
    {
        $performanceReviewList = TestDataService::loadObjectList(PerformanceReview::class, $this->fixture, 'PerformanceReview');
        $expected = [$performanceReviewList[1], $performanceReviewList[2], $performanceReviewList[3]];

        $performanceReviewSearchAndFilterParams = new ReviewListSearchFilterParams();
        $result = $this->performanceReviewDao->getReviewList($performanceReviewSearchAndFilterParams);
        for ($i = 0; $i < count($result); $i++) {
            $this->assertEquals($expected[$i]->getId(), $result[$i]->getId());
            $this->assertEquals($expected[$i]->getStatusId(), $result[$i]->getStatusId());
            $this->assertEquals($expected[$i]->getWorkPeriodStart(), $result[$i]->getWorkPeriodStart());
            $this->assertEquals($expected[$i]->getWorkPeriodEnd(), $result[$i]->getWorkPeriodEnd());
            $this->assertEquals($expected[$i]->getDueDate(), $result[$i]->getDueDate());
            $this->assertEquals($expected[$i]->getDepartment()->getId(), $result[$i]->getDepartment()->getId());
        }
    }

    public function testGetReviewListCount(): void
    {
        $performanceReviewSearchAndFilterParams = new ReviewListSearchFilterParams();
        $result = $this->performanceReviewDao->getReviewListCount($performanceReviewSearchAndFilterParams);
        $this->assertEquals(3, $result);
    }

    public function testGetReviewIdsBySupervisorId(): void
    {
        $expected = [11, 12, 13, 14];
        $result = $this->performanceReviewDao->getReviewIdsBySupervisorId(1);
        $this->assertEquals($expected, $result);
    }
}

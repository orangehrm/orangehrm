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

namespace OrangeHRM\Tests\Leave\Dao;

use DateTime;
use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveComment;
use OrangeHRM\Leave\Dao\LeaveCommentDao;
use OrangeHRM\Leave\Dto\LeaveCommentSearchFilterParams;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Leave
 * @group Dao
 */
class LeaveCommentDaoTest extends TestCase
{
    private LeaveCommentDao $leaveCommentDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->leaveCommentDao = new LeaveCommentDao();
        $this->fixture = Config::get(
                Config::PLUGINS_DIR
            ) . '/orangehrmLeavePlugin/test/fixtures/LeaveCommentDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testSearchLeaveComment(): void
    {
        $leaveCommentSearchParams = new LeaveCommentSearchFilterParams();
        $leaveCommentSearchParams->setLeaveById(1);
        $result = $this->leaveCommentDao->searchLeaveComments($leaveCommentSearchParams);
        $this->assertCount(4, $result);
        $this->assertTrue($result[0] instanceof LeaveComment);
        //check order
        $this->assertEquals(2, $result[0]->getId());
        $this->assertEquals(4, $result[1]->getId());
        $this->assertEquals(1, $result[2]->getId());
        $this->assertEquals(3, $result[3]->getId());
    }

    public function testSaveLeaveComment(): void
    {
        $leaveComment = new LeaveComment();
        $leaveComment->getDecorator()->setLeaveById(1);
        $leaveComment->setComment('test comment');
        $dateTime = new DateTime('2020-12-25 07:20:21');
        $leaveComment->setCreatedAt($dateTime);
        $leaveComment->getDecorator()->setCreatedByEmployeeByEmpNumber(1);
        $leaveComment->getDecorator()->setCreatedByUserById(1);

        $result = $this->leaveCommentDao->saveLeaveComment($leaveComment);
        $this->assertTrue($result instanceof LeaveComment);
        $this->assertEquals(8, $result->getId());
        $this->assertEquals("test comment", $result->getComment());
        $this->assertEquals($dateTime, $result->getCreatedAt());
        $this->assertEquals(1, $result->getCreatedByEmployee()->getEmpNumber());
        $this->assertEquals(1, $result->getCreatedBy()->getId());
    }

    public function testGetSearchLeaveCommentsCount(): void
    {
        $leaveCommentSearchParams = new LeaveCommentSearchFilterParams();
        $leaveCommentSearchParams->setLeaveById(1);
        $result = $this->leaveCommentDao->getSearchLeaveCommentsCount($leaveCommentSearchParams);
        $this->assertEquals(4, $result);
    }

    public function testGetLeaveById(): void
    {
        $leave = $this->leaveCommentDao->getLeaveById(1);

        $this->assertTrue($leave instanceof Leave);
        $this->assertEquals(1, $leave->getId());

        $leave = $this->leaveCommentDao->getLeaveById(6);
        $this->assertTrue($leave == null);
    }
}

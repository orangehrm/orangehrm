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

/**
 * @group buzz
 * @group buzzNotifications
 */
class BuzzNotificationServiceTest extends PHPUnit\Framework\TestCase
{
    private $buzzNotificationService = null;

    protected function setUp()
    {
        $this->buzzNotificationService = new BuzzNotificationService();
    }

    public function testGetBuzzNotificationMetadata()
    {
        $empId = 1;
        $now = date("Y-m-d H:i:s");
        $buzzNotificationMetaData = new BuzzNotificationMetadata();
        $buzzNotificationMetaData->setEmpNumber($empId);
        $buzzNotificationMetaData->setLastNotificationViewTime($now);

        $buzzNotificationDao = $this->getMockBuilder('BuzzNotificationDao')
            ->setMethods(array('getBuzzNotificationMetadata'))
            ->getMock();
        $buzzNotificationDao->expects($this->once())
            ->method('getBuzzNotificationMetadata')
            ->with($empId)
            ->will($this->returnValue($buzzNotificationMetaData));
        $this->buzzNotificationService->setBuzzNotificationDao($buzzNotificationDao);
        $resultBuzzNotificationMetaData = $this->buzzNotificationService->getBuzzNotificationMetadata($empId);

        $this->assertEquals($empId, $resultBuzzNotificationMetaData->getEmpNumber());
        $this->assertEquals($now, $resultBuzzNotificationMetaData->getLastNotificationViewTime());
    }

    public function testSaveBuzzNotificationMetadata()
    {
        $empId = 1;
        $now = date("Y-m-d H:i:s");
        $buzzNotificationMetaData = new BuzzNotificationMetadata();
        $buzzNotificationMetaData->setEmpNumber($empId);
        $buzzNotificationMetaData->setLastNotificationViewTime($now);

        $buzzNotificationDao = $this->getMockBuilder('BuzzNotificationDao')
            ->setMethods(array('saveBuzzNotificationMetadata'))
            ->getMock();
        $buzzNotificationDao->expects($this->once())
            ->method('saveBuzzNotificationMetadata')
            ->will($this->returnValue($buzzNotificationMetaData));
        $this->buzzNotificationService->setBuzzNotificationDao($buzzNotificationDao);
        $resultBuzzNotificationMetaData = $this->buzzNotificationService->saveBuzzNotificationMetadata($buzzNotificationMetaData);

        $this->assertEquals($empId, $resultBuzzNotificationMetaData->getEmpNumber());
        $this->assertEquals($now, $resultBuzzNotificationMetaData->getLastNotificationViewTime());
        $this->assertEquals(null, $resultBuzzNotificationMetaData->getLastClearNotifications());
    }

    /**
     * @dataProvider getSharesExceptEmployeeNumberSinceDataProvider
     * @param $empId
     * @param $since
     * @param $args
     * @param $shareArray
     * @param $expectedCount
     * @throws DaoException
     */
    public function testGetSharesExceptEmployeeNumberSince($empId, $since, $args, $shareArray, $expectedCount)
    {
        $buzzNotificationDao = $this->getMockBuilder('BuzzNotificationDao')
            ->setMethods(array('getSharesExceptEmployeeNumberSince'))
            ->getMock();
        $buzzNotificationDao->expects($this->once())
            ->method('getSharesExceptEmployeeNumberSince')
            ->with(...$args)
            ->will($this->returnValue($shareArray));
        $this->buzzNotificationService->setBuzzNotificationDao($buzzNotificationDao);
        $resultShares = $this->buzzNotificationService->getSharesExceptEmployeeNumberSince(...$args);
        $this->assertEquals($expectedCount, count($resultShares));
    }

    /**
     * @return Generator
     * @throws Doctrine_Connection_Exception
     * @throws Doctrine_Record_Exception
     */
    public function getSharesExceptEmployeeNumberSinceDataProvider()
    {
        $empId = 1;
        $since = new DateTime("2020-05-01 00:00:00");
        $share1 = new Share();
        $share2 = new Share();
        $share3 = new Share();

        yield [$empId, $since, [$empId, $since], [$share1, $share2], 2];
        yield [$empId, $since, [$empId, null], [$share1, $share2, $share3], 3];
    }

    /**
     * @dataProvider getCommentsOnEmployeePostsSinceDataProvider
     * @param $args
     * @param $commentsArray
     * @param $expectedCount
     * @throws DaoException
     */
    public function testGetCommentsOnEmployeePostsSince($args, $commentsArray, $expectedCount)
    {
        $buzzNotificationDao = $this->getMockBuilder('BuzzNotificationDao')
            ->setMethods(array('getCommentsOnEmployeePostsSince'))
            ->getMock();
        $buzzNotificationDao->expects($this->once())
            ->method('getCommentsOnEmployeePostsSince')
            ->with(...$args)
            ->will($this->returnValue($commentsArray));
        $this->buzzNotificationService->setBuzzNotificationDao($buzzNotificationDao);
        $commentsOnEmployeePostsSince = $this->buzzNotificationService->getCommentsOnEmployeePostsSince(...$args);
        $this->assertEquals($expectedCount, count($commentsOnEmployeePostsSince));
    }

    /**
     * @return Generator
     * @throws Doctrine_Connection_Exception
     * @throws Doctrine_Record_Exception
     */
    public function getCommentsOnEmployeePostsSinceDataProvider()
    {
        $comment1 = new Comment();
        $comment2 = new Comment();
        $comment3 = new Comment();
        $empId = 1;
        $since = new DateTime("2020-05-01 00:00:00");

        yield [[$empId, $since], [$comment1, $comment2], 2];
        yield [[$empId, $since, true], [$comment1, $comment2], 2];
        yield [[$empId, $since, false], [$comment1, $comment2, $comment3], 3];
    }

    /**
     * @dataProvider getLikesOnEmployeePostsSinceDataProvider
     * @param $args
     * @param $likeOnShareArray
     * @param $expectedCount
     * @throws DaoException
     */
    public function testGetLikesOnEmployeePostsSince($args, $likeOnShareArray, $expectedCount)
    {
        $buzzNotificationDao = $this->getMockBuilder('BuzzNotificationDao')
            ->setMethods(array('getLikesOnEmployeePostsSince'))
            ->getMock();
        $buzzNotificationDao->expects($this->once())
            ->method('getLikesOnEmployeePostsSince')
            ->with(...$args)
            ->will($this->returnValue($likeOnShareArray));
        $this->buzzNotificationService->setBuzzNotificationDao($buzzNotificationDao);
        $likesOnEmployeePostsSince = $this->buzzNotificationService->getLikesOnEmployeePostsSince(...$args);
        $this->assertEquals($expectedCount, count($likesOnEmployeePostsSince));
    }

    /**
     * @return Generator
     * @throws Doctrine_Connection_Exception
     * @throws Doctrine_Record_Exception
     */
    public function getLikesOnEmployeePostsSinceDataProvider()
    {
        $likeOnShare1 = new LikeOnShare();
        $likeOnShare2 = new LikeOnShare();
        $likeOnShare3 = new LikeOnShare();
        $empId = 1;
        $since = new DateTime("2020-05-01 00:00:00");

        yield [[$empId, $since], [$likeOnShare1, $likeOnShare2], 2];
        yield [[$empId, $since, true], [$likeOnShare1, $likeOnShare2], 2];
        yield [[$empId, $since, false], [$likeOnShare1, $likeOnShare2, $likeOnShare3], 3];
    }

    /**
     * @dataProvider getLikesOnEmployeeCommentsSinceDataProvider
     * @param $args
     * @param $likeOnCommentArray
     * @param $expectedCount
     * @throws DaoException
     */
    public function testGetLikesOnEmployeeCommentsSince($args, $likeOnCommentArray, $expectedCount)
    {
        $buzzNotificationDao = $this->getMockBuilder('BuzzNotificationDao')
            ->setMethods(array('getLikesOnEmployeeCommentsSince'))
            ->getMock();
        $buzzNotificationDao->expects($this->once())
            ->method('getLikesOnEmployeeCommentsSince')
            ->with(...$args)
            ->will($this->returnValue($likeOnCommentArray));
        $this->buzzNotificationService->setBuzzNotificationDao($buzzNotificationDao);
        $likesOnEmployeeCommentsSince = $this->buzzNotificationService->getLikesOnEmployeeCommentsSince(...$args);
        $this->assertEquals($expectedCount, count($likesOnEmployeeCommentsSince));
    }

    /**
     * @return Generator
     * @throws Doctrine_Connection_Exception
     * @throws Doctrine_Record_Exception
     */
    public function getLikesOnEmployeeCommentsSinceDataProvider()
    {
        $likeOnComment1 = new LikeOnComment();
        $likeOnComment2 = new LikeOnComment();
        $likeOnComment3 = new LikeOnComment();
        $empId = 1;
        $since = new DateTime("2020-05-01 00:00:00");

        yield [[$empId, $since], [$likeOnComment1, $likeOnComment2], 2];
        yield [[$empId, $since, true], [$likeOnComment1, $likeOnComment2], 2];
        yield [[$empId, $since, false], [$likeOnComment1, $likeOnComment2, $likeOnComment3], 3];
    }

    /**
     * @dataProvider getSharesOfEmployeePostsSinceDataProvider
     * @param $args
     * @param $shareArray
     * @param $expectedCount
     * @throws DaoException
     */
    public function testGetSharesOfEmployeePostsSince($args, $shareArray, $expectedCount)
    {
        $buzzNotificationDao = $this->getMockBuilder('BuzzNotificationDao')
            ->setMethods(array('getSharesOfEmployeePostsSince'))
            ->getMock();
        $buzzNotificationDao->expects($this->once())
            ->method('getSharesOfEmployeePostsSince')
            ->with(...$args)
            ->will($this->returnValue($shareArray));
        $this->buzzNotificationService->setBuzzNotificationDao($buzzNotificationDao);
        $resultShares = $this->buzzNotificationService->getSharesOfEmployeePostsSince(...$args);
        $this->assertEquals($expectedCount, count($resultShares));
    }

    /**
     * @return Generator
     * @throws Doctrine_Connection_Exception
     * @throws Doctrine_Record_Exception
     */
    public function getSharesOfEmployeePostsSinceDataProvider()
    {
        $empId = 1;
        $since = new DateTime("2020-05-01 00:00:00");
        $share1 = new Share();
        $share2 = new Share();
        $share3 = new Share();

        yield [[$empId, $since], [$share1, $share2], 2];
        yield [[$empId, $since, true], [$share1, $share2, $share3], 3];
        yield [[$empId, null, false], [$share1, $share2], 2];
    }

    /**
     * @dataProvider timeElapsedStringDataProvider
     */
    public function testTimeElapsedString($args, $returnValue, $expected)
    {
        $buzzNotificationService = $this->getMockBuilder('BuzzNotificationService')
            ->setMethods(array('getUserNow'))
            ->getMock();
        $buzzNotificationService->expects($this->once())
            ->method('getUserNow')
            ->will($this->returnValue($returnValue));
        $result = $buzzNotificationService->timeElapsedString(...$args);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return Generator
     * @throws Exception
     */
    public function timeElapsedStringDataProvider()
    {
        $userNow = new DateTime("2020-05-02 00:00:00");

        // Localization applied for strings in BuzzNotificationService::timeElapsedString service
        yield [[new DateTime("2020-05-03 00:00:00")], $userNow, '1 day ago'];
        yield [[new DateTime("2020-05-04 00:00:00")], $userNow, '2 days ago'];
        yield [[new DateTime("2020-05-02 00:01:00")], $userNow, '1 minute ago'];
        yield [[new DateTime("2020-05-02 00:05:00")], $userNow, '5 minutes ago'];
        yield [[new DateTime("2020-05-02 01:01:00")], $userNow, '1 hour ago'];
        yield [[new DateTime("2020-05-02 06:05:00")], $userNow, '6 hours ago'];
        yield [[new DateTime("2020-05-09 00:00:00")], $userNow, '1 week ago'];
        yield [[new DateTime("2020-05-16 00:00:00")], $userNow, '2 weeks ago'];
        yield [[new DateTime("2020-05-02 00:00:00")], $userNow, 'Just now'];
    }

}

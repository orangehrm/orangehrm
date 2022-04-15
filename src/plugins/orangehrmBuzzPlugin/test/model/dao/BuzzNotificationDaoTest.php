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
class BuzzNotificationDaoTest extends PHPUnit\Framework\TestCase
{
    private $buzzNotificationDao = null;

    /**
     * Set up method
     */
    protected function setUp()
    {
        $this->buzzNotificationDao = new BuzzNotificationDao();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmBuzzPlugin/test/fixtures/OrangeBuzz.yml';
        TestDataService::populate($this->fixture);
    }

    public function testSaveBuzzNotificationMetadata()
    {
        $buzzNotificationMetadata = new BuzzNotificationMetadata();
        $buzzNotificationMetadata->setEmpNumber(100);
        $now = date("Y-m-d H:i:s");
        $buzzNotificationMetadata->setLastNotificationViewTime($now);
        $resultBuzzNotificationMetadata = $this->buzzNotificationDao->saveBuzzNotificationMetadata($buzzNotificationMetadata);
        $this->assertEquals($now, $resultBuzzNotificationMetadata->getLastNotificationViewTime());
        $this->assertEquals(null, $resultBuzzNotificationMetadata->getLastClearNotifications());
        $this->assertEquals(null, $resultBuzzNotificationMetadata->getLastBuzzViewTime());

        $buzzNotificationMetadata->setLastClearNotifications($now);
        $resultBuzzNotificationMetadata = $this->buzzNotificationDao->saveBuzzNotificationMetadata($resultBuzzNotificationMetadata);
        $this->assertEquals($now, $resultBuzzNotificationMetadata->getLastNotificationViewTime());
        $this->assertEquals($now, $resultBuzzNotificationMetadata->getLastClearNotifications());
        $this->assertEquals(null, $resultBuzzNotificationMetadata->getLastBuzzViewTime());

        $buzzNotificationMetadata->setLastBuzzViewTime($now);
        $resultBuzzNotificationMetadata = $this->buzzNotificationDao->saveBuzzNotificationMetadata($resultBuzzNotificationMetadata);
        $this->assertEquals($now, $resultBuzzNotificationMetadata->getLastNotificationViewTime());
        $this->assertEquals($now, $resultBuzzNotificationMetadata->getLastClearNotifications());
        $this->assertEquals($now, $resultBuzzNotificationMetadata->getLastBuzzViewTime());
    }

    public function testGetBuzzNotificationMetadata()
    {
        $resultBuzzNotificationMetadata = $this->buzzNotificationDao->getBuzzNotificationMetadata(1);
        $this->assertEquals('2014-01-01 00:00:00', $resultBuzzNotificationMetadata->getLastNotificationViewTime());
        $this->assertEquals(null, $resultBuzzNotificationMetadata->getLastClearNotifications());
        $this->assertEquals('2014-01-01 00:00:00', $resultBuzzNotificationMetadata->getLastBuzzViewTime());

        $resultBuzzNotificationMetadata = $this->buzzNotificationDao->getBuzzNotificationMetadata(2);
        $this->assertEquals('2014-01-03 00:00:00', $resultBuzzNotificationMetadata->getLastNotificationViewTime());
        $this->assertEquals('2014-01-02 00:00:00', $resultBuzzNotificationMetadata->getLastClearNotifications());
        $this->assertEquals('2014-01-01 00:00:00', $resultBuzzNotificationMetadata->getLastBuzzViewTime());
    }

    public function testGetSharesExceptEmployeeNumberSince()
    {
        $since = new DateTime("2015-01-01 00:00:00");
        $resultShares = $this->buzzNotificationDao->getSharesExceptEmployeeNumberSince(1, $since);
        $this->assertEquals(2, count($resultShares));
        $this->assertTrue($resultShares[0] instanceof Share);
        $this->assertEquals('2', $resultShares[0]->getEmployeeNumber());
        $this->assertEquals('2016-01-01 00:00:00', $resultShares[0]->getShareTime());
        $this->assertEquals(3, $resultShares[0]->getId());
    }

    public function testGetCommentsOnEmployeePostsSince()
    {
        $since = new DateTime("2014-01-01 00:00:00");
        $comments = $this->buzzNotificationDao->getCommentsOnEmployeePostsSince(1, $since);
        $this->assertEquals(1, count($comments));
        $this->assertTrue($comments[0] instanceof Comment);
        $this->assertEquals('2', $comments[0]->getEmployeeNumber());
        $this->assertEquals('2014-01-02 00:00:00', $comments[0]->getCommentTime());
        $this->assertEquals(1, $comments[0]->getShareId());
    }

    public function testGetLikesOnEmployeePostsSince()
    {
        $since = new DateTime("2014-01-01 00:00:00");
        $likeOnShares = $this->buzzNotificationDao->getLikesOnEmployeePostsSince(1, $since);
        $this->assertEquals(1, count($likeOnShares));
        $this->assertTrue($likeOnShares[0] instanceof LikeOnShare);
        $this->assertEquals('2', $likeOnShares[0]->getEmployeeNumber());
        $this->assertEquals('2014-01-03 00:00:00', $likeOnShares[0]->getLikeTime());
        $this->assertEquals(1, $likeOnShares[0]->getShareId());
    }

    public function testGetLikesOnEmployeeCommentsSince()
    {
        $since = new DateTime("2013-12-31 00:00:00");
        $likeOnShares = $this->buzzNotificationDao->getLikesOnEmployeeCommentsSince(1, $since);
        $this->assertEquals(1, count($likeOnShares));
        $this->assertTrue($likeOnShares[0] instanceof LikeOnComment);
        $this->assertEquals('2', $likeOnShares[0]->getEmployeeNumber());
        $this->assertEquals('2014-01-01 00:00:00', $likeOnShares[0]->getLikeTime());
        $this->assertEquals(1, $likeOnShares[0]->getCommentLike()->getShareId());

        $since = new DateTime("2014-01-01 00:00:00");
        $likeOnShares = $this->buzzNotificationDao->getLikesOnEmployeeCommentsSince(1, $since);
        $this->assertEquals(0, count($likeOnShares));
    }

    public function testGetSharesOfEmployeePostsSince()
    {
        $since = new DateTime("2015-01-01 00:00:00");
        $sharesOfEmployeePosts = $this->buzzNotificationDao->getSharesOfEmployeePostsSince(2, $since);
        $this->assertEquals(1, count($sharesOfEmployeePosts));
        $this->assertTrue($sharesOfEmployeePosts[0] instanceof Share);
        $this->assertEquals('3', $sharesOfEmployeePosts[0]->getEmployeeNumber());
        $this->assertEquals('2015-01-03 00:00:00', $sharesOfEmployeePosts[0]->getShareTime());
        $this->assertEquals(5, $sharesOfEmployeePosts[0]->getId());

        $since = new DateTime("2015-01-03 00:00:00");
        $sharesOfEmployeePosts = $this->buzzNotificationDao->getLikesOnEmployeeCommentsSince(2, $since);
        $this->assertEquals(0, count($sharesOfEmployeePosts));
    }
}

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
 */
class BuzzServiceTest extends PHPUnit\Framework\TestCase {

    private $buzzService, $employeeService;

    /**
     * Set up method
     */
    protected function setUp(): void {
        $this->buzzService = new BuzzService();
        $this->employeeService = $this->buzzService->getEmployeeService();
    }

    public function testGetBuzzDao() {
        $buzzDao = $this->buzzService->getBuzzDao();
        $this->assertTrue($buzzDao instanceof BuzzDao);
    }

    /**
     * this is function to test saving post in the database
     */
    public function testGetShareCount() {
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getSharesCount'))
			->getMock();
        $buzzDao->expects($this->once())
                ->method('getSharesCount')
                ->will($this->returnValue(4));
        $this->buzzService->setBuzzDao($buzzDao);

        $resultShareCount = $this->buzzService->getSharesCount();
        $this->assertEquals(4, $resultShareCount);
    }

    /**
     * this is function to test saving post in the database
     */
    public function testSavePost() {
        $post = New Post();
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('savePost'))
			->getMock();
        $buzzDao->expects($this->once())
                ->method('savePost')
                ->with($post)
                ->will($this->returnValue($post));
        $this->buzzService->setBuzzDao($buzzDao);

        $result = $this->buzzService->savePost($post);
        $this->assertTrue($result instanceof Post);
    }

    /**
     * this is function to test saving link in the database
     */
    public function testSaveLink() {
        $link = New Link();
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('saveLink'))
			->getMock();
        $buzzDao->expects($this->once())
                ->method('saveLink')
                ->with($link)
                ->will($this->returnValue($link));
        $this->buzzService->setBuzzDao($buzzDao);
        $resultLink = $this->buzzService->saveLink($link);

        $this->assertTrue($resultLink instanceof Link);
    }

    /**
     * this is function to test getting shares data from the database
     */
    public function testGetShares() {
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getShares'))
			->getMock();
        $shareArray = array(
            new Share(),
            new Share(),
            new Share()
        );
        $buzzDao->expects($this->once())
                ->method('getShares')
                ->with(2)
                ->will($this->returnValue($shareArray));
        $this->buzzService->setBuzzDao($buzzDao);
        $resultShares = $this->buzzService->getShares(2);

        $this->assertTrue(is_array($resultShares));
        $this->assertEquals(3, count($resultShares));
    }

    /**
     * this is function to test getting post from share
     */
    public function testGetPostFromShare() {
        $post = new Post();
        $share = new Share();
        $share->setPostShared($post);
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getShareById'))
			->getMock();
        $buzzDao->expects($this->once())
                ->method('getShareById')
                ->with(1)
                ->will($this->returnValue($share));
        $this->buzzService->setBuzzDao($buzzDao);
        $resultShare = $this->buzzService->getShareById(1);

        $this->assertTrue($resultShare->getPostShared() instanceof Post);
    }

    /**
     * this is function to test delete post
     */
    public function testDeletePost() {
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('deletePost'))
			->getMock();
        $buzzDao->expects($this->once())
                ->method('deletePost')
                ->with('1')
                ->will($this->returnValue(1));
        $this->buzzService->setBuzzDao($buzzDao);
        $result = $this->buzzService->deletePost('1');

        $this->assertEquals('1', $result);
    }

    /**
     * this is function to test delete shares from the database
     */
    public function testDeleteShare() {
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('deleteShare'))
			->getMock();
        $buzzDao->expects($this->once())
                ->method('deleteShare')
                ->with('1')
                ->will($this->returnValue('1'));
        $this->buzzService->setBuzzDao($buzzDao);
        $result = $this->buzzService->deleteShare('1');

        $this->assertEquals(1, $result);
    }

    /**
     * this is function to test save likes on share to database
     */
    public function testLikeOnShare() {
        $like = new LikeOnShare();
        $like->setEmployeeNumber(1);
        $like->setLikeTime('2015-01-10 12:12:12');
        $share = new Share();
        $share->setId(5);
        $share->setPostId(2);
        $share->setNumberOfLikes(1);
        $like->setShareLike($share);

        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('saveLikeForShare', 'saveShare'))
			->getMock();
        $buzzDao->expects($this->once())
                ->method('saveLikeForShare')
                ->with($like)
                ->will($this->returnValue($like));
        $buzzDao->expects($this->once())
                ->method('saveShare')
                ->with($share)
                ->will($this->returnValue(null));
        $this->buzzService->setBuzzDao($buzzDao);
        $result = $this->buzzService->saveLikeForShare($like);

        $this->assertEquals('2015-01-10 12:12:12', $result->getLikeTime());
    }

    /**
     * this is function to test delete likes on share
     */
    public function testDeleteLikeOnshare() {
        $like = new LikeOnShare();
        $like->setId(20);
        $share = new Share();
        $share->setNumberOfLikes(1);
        $like->setShareLike($share);
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('deleteLikeForShare', 'saveShare'))
			->getMock();

        $buzzDao->expects($this->once())
                ->method('deleteLikeForShare')
                ->with($like)
                ->will($this->returnValue(1));
        $buzzDao->expects($this->once())
                ->method('saveShare')
                ->with($share)
                ->will($this->returnValue(null));
        $this->buzzService->setBuzzDao($buzzDao);
        $result = $this->buzzService->deleteLikeForShare($like);

        $this->assertEquals('1', $result);
    }

    /**
     * this is function to test save comment on share to database
     */
    public function testCommentOnShare() {
        $comment = new Comment();
        $comment->setCommentTime('2015-01-10 12:12:12');
        $share = new Share();
        $share->setId(7);
        $share->setPostId(2);
        $share->setNumberOfLikes(1);
        $comment->setShareComment($share);

        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('saveCommentShare', 'saveShare'))
			->getMock();

        $buzzDao->expects($this->once())
                ->method('saveCommentShare')
                ->with($comment)
                ->will($this->returnValue($comment));

        $buzzDao->expects($this->once())
                ->method('saveShare')
                ->with($share)
                ->will($this->returnValue(null));

        $this->buzzService->setBuzzDao($buzzDao);
        $result = $this->buzzService->saveCommentShare($comment);

        $this->assertEquals('2015-01-10 12:12:12', $result->getCommentTime());
    }

    /**
     * this is function to test delete Comment on share
     */
    public function testDeleteCommentOnShare() {
        $comment = new Comment();
        $share = new Share();
        $share->setId(6);
        $share->setPostId(2);
        $share->setNumberOfLikes(1);
        $comment->setShareComment($share);
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('deleteCommentForShare', 'saveShare'))
			->getMock();

        $buzzDao->expects($this->once())
                ->method('deleteCommentForShare')
                ->with($comment)
                ->will($this->returnValue(1));
        $buzzDao->expects($this->once())
                ->method('saveShare')
                ->with($share)
                ->will($this->returnValue(null));
        $this->buzzService->setBuzzDao($buzzDao);

        $result = $this->buzzService->deleteCommentForShare($comment);
        $this->assertEquals(1, $result);
    }

    /**
     * this is function to test save likes on comment
     */
    public function testLikeOnComment() {
        $like = new LikeOnComment();
        $like->setLikeTime('2015-01-10 12:12:12');
        $comment = new Comment();
        $comment->setNumberOfLikes(3);
        $like->setCommentLike($comment);
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('saveLikeForComment', 'saveCommentShare'))
			->getMock();

        $buzzDao->expects($this->once())
                ->method('saveLikeForComment')
                ->with($like)
                ->will($this->returnValue($like));
        $buzzDao->expects($this->once())
                ->method('saveCommentShare')
                ->with($comment);

        $this->buzzService->setBuzzDao($buzzDao);

        $result = $this->buzzService->saveLikeForComment($like);
        $this->assertEquals('2015-01-10 12:12:12', $result->getLikeTime());
    }

    /**
     * this is function to test delete likes on the comment
     */
    public function testDeletLikeOnComment() {
        $like = new LikeOnComment();
        $like->setId(20);
        $like->setCommentId(1);
        $comment = new Comment();
        $comment->setNumberOfLikes(3);
        $like->setCommentLike($comment);
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('deleteLikeForComment', 'saveCommentShare'))
			->getMock();

        $buzzDao->expects($this->once())
                ->method('deleteLikeForComment')
                ->with($like)
                ->will($this->returnValue(1));
        $buzzDao->expects($this->once())
                ->method('saveCommentShare')
                ->with($comment);
        $this->buzzService->setBuzzDao($buzzDao);

        $result = $this->buzzService->deleteLikeForComment($like);
        $this->assertEquals(1, $result);
    }

    /**
     * this is functoin to test get share by id
     */
    public function testGetShareById() {

        $share = new Share();

        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getShareById'))
			->getMock();

        $buzzDao->expects($this->once())
                ->method('getShareById')
                ->with(1)
                ->will($this->returnValue($share));

        $this->buzzService->setBuzzDao($buzzDao);
        $result = $this->buzzService->getShareById(1);

        $this->assertTrue($result instanceof Share);
    }

    /**
     * this is functoin to test get post by id
     */
    public function testGetPostById() {
        $post = new Post();

        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getPostById'))
			->getMock();

        $buzzDao->expects($this->once())
                ->method('getPostById')
                ->with(1)
                ->will($this->returnValue($post));

        $this->buzzService->setBuzzDao($buzzDao);
        $result = $this->buzzService->getPostById(1);

        $this->assertTrue($result instanceof Post);
    }

    /**
     * this is functoin to test get Comment by id
     */
    public function testGetCommentById() {
        $comment = new Comment();
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getCommentById'))
			->getMock();

        $buzzDao->expects($this->once())
                ->method('getCommentById')
                ->with(1)
                ->will($this->returnValue($comment));

        $this->buzzService->setBuzzDao($buzzDao);
        $result = $this->buzzService->getCommentById(1);

        $this->assertTrue($result instanceof Comment);
    }

    /**
     * this is functoin to test get likeOnComment by id
     */
    public function testGetLikeOnCommentById() {
        $like = New LikeOnComment();
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getLikeOnCommentById'))
			->getMock();

        $buzzDao->expects($this->once())
                ->method('getLikeOnCommentById')
                ->with(1)
                ->will($this->returnValue($like));

        $this->buzzService->setBuzzDao($buzzDao);
        $result = $this->buzzService->getLikeOnCommentById(1);


        $this->assertTrue($result instanceof LikeOnComment);
    }

    /**
     * this is functoin to test get likeOnShare by id
     */
    public function testGetLikeOnshareById() {
        $like = New LikeOnShare();
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getLikeOnShareById'))
			->getMock();

        $buzzDao->expects($this->once())
                ->method('getLikeOnShareById')
                ->with(1)
                ->will($this->returnValue($like));

        $this->buzzService->setBuzzDao($buzzDao);
        $result = $this->buzzService->getLikeOnShareById(1);


        $this->assertTrue($result instanceof LikeOnShare);
    }

    /**
     * this is function to test save likes on share to database
     */
    public function testUnLikeOnShare() {
        $unlikeOnShare = new UnLikeOnShare();
        $unlikeOnShare->setShareId(1);
        $unlikeOnShare->setEmployeeNumber(1);
        $unlikeOnShare->setLikeTime('2015-01-10 12:12:12');
        $share = new Share();
        $share->setNumberOfLikes(1);
        $unlikeOnShare->setShareUnLike($share);
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('saveUnLikeForShare', 'saveShare'))
			->getMock();

        $buzzDao->expects($this->once())
                ->method('saveUnLikeForShare')
                ->with($unlikeOnShare)
                ->will($this->returnValue($unlikeOnShare));
        $buzzDao->expects($this->once())
                ->method('saveShare')
                ->with($share)
                ->will($this->returnValue(null));
        $this->buzzService->setBuzzDao($buzzDao);

        $resultUnlikeOnShare = $this->buzzService->saveUnLikeForShare($unlikeOnShare);
        $this->assertEquals('2015-01-10 12:12:12', $resultUnlikeOnShare->getLikeTime());
    }

    /**
     * this is function to test delete likes on share
     */
    public function testDeleteUnLikeOnshare() {
        $unlikeOnShare = new UnLikeOnShare();
        $unlikeOnShare->setId(20);
        $unlikeOnShare->setShareId(1);
        $unlikeOnShare->setEmployeeNumber(1);
        $unlikeOnShare->setLikeTime('2015-01-10 12:12:12');
        $share = new Share();
        $share->setNumberOfLikes(1);
        $unlikeOnShare->setShareUnLike($share);
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('deleteUnLikeForShare', 'saveShare'))
			->getMock();

        $buzzDao->expects($this->once())
                ->method('deleteUnLikeForShare')
                ->with($unlikeOnShare)
                ->will($this->returnValue(1));
        $buzzDao->expects($this->once())
                ->method('saveShare')
                ->with($share)
                ->will($this->returnValue(null));
        $this->buzzService->setBuzzDao($buzzDao);

        $resultDeleteCount = $this->buzzService->deleteUnLikeForShare($unlikeOnShare);
        $this->assertEquals(1, $resultDeleteCount);
    }

    /**
     * this is function to test save likes on comment
     */
    public function testUnLikeOnComment() {
        $like = new UnLikeOnComment();
        $like->setCommentId(1);
        $like->setEmployeeNumber(1);
        $like->setLikeTime('2015-01-10 12:12:12');

        $comment = new Comment();
        $comment->setNumberOfLikes(1);
        $like->setCommentUnLike($comment);
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('saveUnLikeForComment', 'saveCommentShare'))
			->getMock();

        $buzzDao->expects($this->once())
                ->method('saveUnLikeForComment')
                ->with($like)
                ->will($this->returnValue($like));
        $buzzDao->expects($this->once())
                ->method('saveCommentShare')
                ->with($comment)
                ->will($this->returnValue(null));
        $this->buzzService->setBuzzDao($buzzDao);

        $resultUnlikeOnComment = $this->buzzService->saveUnLikeForComment($like);
        $this->assertEquals('2015-01-10 12:12:12', $resultUnlikeOnComment->getLikeTime());
    }

    /**
     * this is function to test delete likes on the comment
     */
    public function testDeletUnLikeOnComment() {
        $like = new UnLikeOnComment();
        $comment = new Comment();
        $comment->setNumberOfLikes(1);
        $like->setCommentUnLike($comment);
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('deleteUnLikeForComment', 'saveCommentShare'))
			->getMock();

        $buzzDao->expects($this->once())
                ->method('deleteUnLikeForComment')
                ->with($like)
                ->will($this->returnValue(1));
        $buzzDao->expects($this->once())
                ->method('saveCommentShare')
                ->with($comment)
                ->will($this->returnValue(null));
        $this->buzzService->setBuzzDao($buzzDao);

        $resultDeleteCount = $this->buzzService->deleteUnLikeForComment($like);
        $this->assertEquals(1, $resultDeleteCount);
    }

    /**
     * test get more shares 
     */
    public function testGetMoreShares() {
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getMoreShares'))
			->getMock();

        $shareArray = array(
            new Share(),
            new Share(),
            new Share()
        );
        $buzzDao->expects($this->once())
                ->method('getMoreShares')
                ->with(1, 0)
                ->will($this->returnValue($shareArray));

        $this->buzzService->setBuzzDao($buzzDao);
        $resultShares = $this->buzzService->getMoreShares(1, 0);
        $this->assertEquals(3, Count($resultShares));
    }

    /**
     * test test get more employee shares
     */
    public function testGetMoreEmployeeShares() {
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getMoreEmployeeSharesByEmployeeNumber'))
			->getMock();

        $shareArray = array(
            new Share(),
            new Share(),
            new Share()
        );
        $buzzDao->expects($this->once())
                ->method('getMoreEmployeeSharesByEmployeeNumber')
                ->with(1, 0, 1)
                ->will($this->returnValue($shareArray));

        $this->buzzService->setBuzzDao($buzzDao);
        $resultShares = $this->buzzService->getMoreEmployeeSharesByEmployeeNumber(1, 0, 1);
        $this->assertEquals(3, Count($resultShares));
    }

    /**
     * test share by emplyee 
     */
    public function testgetSharesByEmployeeNumber() {
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getSharesByEmployeeNumber'))
			->getMock();

        $shareArray = array(
            new Share(),
            new Share(),
            new Share()
        );
        $buzzDao->expects($this->once())
                ->method('getSharesByEmployeeNumber')
                ->with(1, 2)
                ->will($this->returnValue($shareArray));

        $this->buzzService->setBuzzDao($buzzDao);
        $resultShares = $this->buzzService->getSharesByEmployeeNumber(1, 2);

        $this->assertEquals(3, count($resultShares));
        $this->assertTrue(is_array($resultShares));
    }

    /**
     * test employee shares up to share id
     */
    public function testgetEmployeeShareUptoShareId() {
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getEmployeeSharesUptoShareId'))
			->getMock();

        $shareArray = array(
            new Share(),
            new Share(),
            new Share()
        );
        $buzzDao->expects($this->once())
                ->method('getEmployeeSharesUptoShareId')
                ->with(1, 2)
                ->will($this->returnValue($shareArray));

        $this->buzzService->setBuzzDao($buzzDao);
        $resultShares = $this->buzzService->getEmployeeSharesUptoShareId(1, 2);
        $this->assertEquals(3, Count($resultShares));
    }

    /**
     * test get shares upto share id
     */
    public function testgetShareUpToShareId() {
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getSharesUptoId'))
			->getMock();

        $shareArray = array(
            new Share(),
            new Share(),
            new Share()
        );
        $buzzDao->expects($this->once())
                ->method('getSharesUptoId')
                ->with(1)
                ->will($this->returnValue($shareArray));

        $this->buzzService->setBuzzDao($buzzDao);
        $resultShares = $this->buzzService->getSharesUptoId(1);
        $this->assertTrue(is_array($resultShares));
    }

    /**
     * test saving photo
     */
    public function testSavingPhoto() {
        $photo = new Photo();
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('savePhoto'))
			->getMock();

        $buzzDao->expects($this->once())
                ->method('savePhoto')
                ->with($photo)
                ->will($this->returnValue($photo));

        $this->buzzService->setBuzzDao($buzzDao);

        $resultPhoto = $this->buzzService->savePhoto($photo);
        $this->assertTrue($resultPhoto instanceof Photo);
    }

    /**
     * test shares by user Id
     */
    public function testGetNoOfSharesBy() {
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getNoOfSharesByEmployeeNumber'))
			->getMock();

        $buzzDao->expects($this->once())
                ->method('getNoOfSharesByEmployeeNumber')
                ->with(1)
                ->will($this->returnValue(1));

        $this->buzzService->setBuzzDao($buzzDao);
        $resultNoOfShares = $this->buzzService->getNoOfSharesByEmployeeNumber(1);

        $this->assertEquals(1, $resultNoOfShares);
    }

    /**
     * test comment by user Id
     */
    public function testGetNoOfCommentBy() {
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getNoOfCommentsByEmployeeNumber'))
			->getMock();

        $buzzDao->expects($this->once())
                ->method('getNoOfCommentsByEmployeeNumber')
                ->with(1)
                ->will($this->returnValue(2));

        $this->buzzService->setBuzzDao($buzzDao);
        $resultNoOfComments = $this->buzzService->getNoOfCommentsByEmployeeNumber(1);

        $this->assertEquals(2, $resultNoOfComments);
    }

    /**
     * test comment by user Id
     */
    public function testGetNoOfCommentFor() {
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getNoOfCommentsForEmployeeByEmployeeNumber'))
			->getMock();

        $buzzDao->expects($this->once())
                ->method('getNoOfCommentsForEmployeeByEmployeeNumber')
                ->with(1)
                ->will($this->returnValue(2));

        $this->buzzService->setBuzzDao($buzzDao);
        $resultNoOfComments = $this->buzzService->getNoOfCommentsForEmployeeByEmployeeNumber(1);

        $this->assertEquals(2, $resultNoOfComments);
    }

    /**
     * test shares by user Id
     */
    public function testGetNoOfShareLikesForEmployeeByEmployeeNumber() {
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getNoOfShareLikesForEmployeeByEmployeeNumber'))
			->getMock();

        $buzzDao->expects($this->once())
                ->method('getNoOfShareLikesForEmployeeByEmployeeNumber')
                ->with(1)
                ->will($this->returnValue(2));

        $this->buzzService->setBuzzDao($buzzDao);
        $resultCount = $this->buzzService->getNoOfShareLikesForEmployeeByEmployeeNumber(1);

        $this->assertEquals(2, $resultCount);
    }

    /**
     * test comment by user Id
     */
    public function testGetNoOfCommentLikeBy() {
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getNoOfCommentLikesForEmployeeByEmployeeNumber'))
			->getMock();

        $buzzDao->expects($this->once())
                ->method('getNoOfCommentLikesForEmployeeByEmployeeNumber')
                ->with(1)
                ->will($this->returnValue(2));

        $this->buzzService->setBuzzDao($buzzDao);
        $resultCount = $this->buzzService->getNoOfCommentLikesForEmployeeByEmployeeNumber(1);

        $this->assertEquals(2, $resultCount);
    }

    /**
     * test get most like shares ids
     */
    public function testMostLikeShares() {
        $shareIds = array(1, 2);

        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getMostLikedShares'))
			->getMock();

        $buzzDao->expects($this->once())
                ->method('getMostLikedShares')
                ->with(2)
                ->will($this->returnValue($shareIds));

        $this->buzzService->setBuzzDao($buzzDao);
        $resultShareIds = $this->buzzService->getMostLikedShares(2);
        $this->assertEquals(2, Count($resultShareIds));
    }

    /**
     * test get most commented shares
     */
    public function testMostCommentedShares() {
        $shareIds = array(1, 2);
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getMostCommentedShares'))
			->getMock();

        $buzzDao->expects($this->once())
                ->method('getMostCommentedShares')
                ->with(2)
                ->will($this->returnValue($shareIds));

        $this->buzzService->setBuzzDao($buzzDao);
        $resultShareIds = $this->buzzService->getMostCommentedShares(2);
        $this->assertEquals(2, Count($resultShareIds));
    }

    /**
     * test get employee anivesary
     */
    public function testAnivesary() {
        $date = '2012-05-15';
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getEmployeesHavingAnniversaryOnMonth'))
			->getMock();

        $employeeArray = array(
            new Employee(),
            new Employee(),
            new Employee()
        );
        $buzzDao->expects($this->once())
                ->method('getEmployeesHavingAnniversaryOnMonth')
                ->with($date)
                ->will($this->returnValue($employeeArray));

        $this->buzzService->setBuzzDao($buzzDao);
        $resultEmployees = $this->buzzService->getEmployeesHavingAnniversaryOnMonth($date);
        $this->assertTrue(is_array($resultEmployees));
    }

    /**
     * test get Employees Having Anniversaries Next Year
     */
    public function testGetEmployeesHavingAnniversariesNextYear() {
        $date = '2012-05-15';
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getEmployeesHavingAnniversariesNextYear'))
			->getMock();

        $employeeArray = array(
            new Employee(),
            new Employee(),
            new Employee()
        );
        $buzzDao->expects($this->once())
                ->method('getEmployeesHavingAnniversariesNextYear')
                ->with($date)
                ->will($this->returnValue($employeeArray));

        $this->buzzService->setBuzzDao($buzzDao);
        $resultEmployees = $this->buzzService->getEmployeesHavingAnniversariesNextYear($date);
        $this->assertTrue(is_array($resultEmployees));
    }

    /**
     * test save shares
     */
    public function testSaveShare() {
        $share = new Share();
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('saveShare'))
			->getMock();

        $buzzDao->expects($this->once())
                ->method('saveShare')
                ->with($share)
                ->will($this->returnValue($share));

        $this->buzzService->setBuzzDao($buzzDao);
        $resultShares = $this->buzzService->saveShare($share);
        $this->assertTrue($resultShares instanceof Share);
    }

    public function testGetPhoto() {
        $id = 3;
        $photo = new Photo();
        $photo->setId($id);

        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getPhoto'))
			->getMock();
        $buzzDao->expects($this->once())
                ->method('getPhoto')
                ->with($id)
                ->will($this->returnValue($photo));
        $this->buzzService->setBuzzDao($buzzDao);
        $returnedPhoto = $this->buzzService->getPhoto($id);

        $this->assertEquals($photo, $returnedPhoto);
    }

    public function testGetPostPhotos() {
        $postId = 31;

        $photo1 = new Photo();
        $photo1->setId(11);
        $photo2 = new Photo();
        $photo2->setId(13);
        $postPhotos = array($photo1, $photo2);

        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getPostPhotos'))
			->getMock();
        $buzzDao->expects($this->once())
                ->method('getPostPhotos')
                ->with($postId)
                ->will($this->returnValue($postPhotos));
        $this->buzzService->setBuzzDao($buzzDao);
        $returnedPhotos = $this->buzzService->getPostPhotos($postId);

        $this->assertEquals($postPhotos, $returnedPhotos);
    }

    public function testGetSharePost() {
        $postId = 4;
        $loggedInEmployeeNumber = '1';
        $newText = 'test Content';
        $firstName = 'James';
        $lastName = 'White';

        $mockEmployee = new Employee();
        $mockEmployee->setFirstName($firstName);
        $mockEmployee->setLastName($lastName);
        $mockEmployee->setEmployeeId($loggedInEmployeeNumber);

        $mockEmployeeService = $this->getMockBuilder('employeeService')
			->getMock();

        $this->buzzService->setEmployeeService($mockEmployeeService);

        $shareObject = $this->buzzService->getSharePost($postId, $loggedInEmployeeNumber, $newText);
        $this->assertTrue($shareObject instanceof Share);
        $this->assertEquals($postId, $shareObject->getPostId());
        $this->assertEquals('0', $shareObject->getNumberOfComments());
        $this->assertEquals('0', $shareObject->getNumberOfLikes());
        $this->assertEquals('0', $shareObject->getNumberOfUnlikes());
        $this->assertEquals($newText, $shareObject->getText());
        $this->assertEquals('1', $shareObject->getType());
    }

    public function testGetSharePostDoneByAdmin() {
        $postId = 4;
        $loggedInEmployeeNumber = '';
        $newText = 'test Content';
        $firstName = 'James';
        $lastName = 'White';

        $mockEmployee = new Employee();
        $mockEmployee->setFirstName($firstName);
        $mockEmployee->setLastName($lastName);
        $mockEmployee->setEmployeeId($loggedInEmployeeNumber);

        $mockEmployeeService = $this->getMockBuilder('employeeService')
			->setMethods( array('getEmployee'))
			->getMock();
        $mockEmployeeService->expects($this->never())
                ->method('getEmployee');
        $this->buzzService->setEmployeeService($mockEmployeeService);

        $shareObject = $this->buzzService->getSharePost($postId, $loggedInEmployeeNumber, $newText);
        $this->assertTrue($shareObject instanceof Share);
        $this->assertEquals($postId, $shareObject->getPostId());
        $this->assertEquals('0', $shareObject->getNumberOfComments());
        $this->assertEquals('0', $shareObject->getNumberOfLikes());
        $this->assertEquals('0', $shareObject->getNumberOfUnlikes());
        $this->assertEquals($newText, $shareObject->getText());
        $this->assertEquals('1', $shareObject->getType());
    }

    public function testGetSharedEmployeeNamesForOnlyOriginalPost() {

        $shareCollection = new Doctrine_Collection('Share');

        $shareOne = new Share();
        $shareOne->setType(0);
        $shareCollection->add($shareOne);

        $post = new Post();
        $post->setShare($shareCollection);

        $shareOne->setPostShared($post);

        $sharedEmpArray = $this->buzzService->getSharedEmployeeNames($shareOne);
        $this->assertTrue(is_array($sharedEmpArray));
        $this->assertEquals(0, count($sharedEmpArray));
    }

    public function testGetSharedEmployeeNamesWhenSharedOnceByAdmin() {

        $shareCollection = new Doctrine_Collection('Share');

        $shareOne = new Share();
        $shareOne->setType(0);
        $shareCollection->add($shareOne);

        $shareTwo = new Share();
        $shareTwo->setType(1);
        $shareCollection->add($shareTwo);

        $post = new Post();
        $post->setShare($shareCollection);

        $shareOne->setPostShared($post);
        $shareTwo->setPostShared($post);

        $sharedEmpArray = $this->buzzService->getSharedEmployeeNames($shareTwo);
        $this->assertTrue(is_array($sharedEmpArray));
        $this->assertEquals(1, count($sharedEmpArray));
        $this->assertEquals(null, $sharedEmpArray[0]['employee_number']);
        $this->assertEquals("Admin", $sharedEmpArray[0]['employee_name']);
        $this->assertEquals("Administrator", $sharedEmpArray[0]['employee_job_title']);
    }

    public function testGetSharedEmployeeNamesWhenSharedByAdminAndEmployees() {

        $empNumberOne = 1;
        $firstNameOne = 'James';
        $lastNameOne = 'White';

        $empNumberTwo = 2;
        $firstNameTwo = 'Peter';
        $lastNameTwo = 'Knowles';

        $shareCollection = new Doctrine_Collection('Share');

        $jobTitleOne = new JobTitle();
        $jobTitleOne->setJobTitleName('CTO');

        $jobTitleTwo = new JobTitle();
        $jobTitleTwo->setJobTitleName('SE');

        $employeeOne = new Employee();
        $employeeOne->setEmpNumber($empNumberOne);
        $employeeOne->setFirstName($firstNameOne);
        $employeeOne->setLastName($lastNameOne);
        $employeeOne->setJobTitle($jobTitleOne);

        $employeeTwo = new Employee();
        $employeeTwo->setEmpNumber($empNumberTwo);
        $employeeTwo->setFirstName($firstNameTwo);
        $employeeTwo->setLastName($lastNameTwo);
        $employeeTwo->setJobTitle($jobTitleTwo);

        $shareOne = new Share();
        $shareOne->setType(0);
        $shareCollection->add($shareOne);

        $shareTwo = new Share();
        $shareTwo->setType(1);
        $shareTwo->setEmployeePostShared($employeeOne);
        $shareTwo->setEmployeeNumber(1);
        $shareCollection->add($shareTwo);

        $shareThree = new Share();
        $shareThree->setType(1);
        $shareThree->setEmployeePostShared($employeeTwo);
        $shareThree->setEmployeeNumber(2);
        $shareCollection->add($shareThree);

        $shareFour = new Share();
        $shareFour->setType(1);
        $shareCollection->add($shareFour);

        $post = new Post();
        $post->setShare($shareCollection);

        $shareOne->setPostShared($post);
        $shareTwo->setPostShared($post);
        $shareThree->setPostShared($post);
        $shareFour->setPostShared($post);

        $sharedEmpArray = $this->buzzService->getSharedEmployeeNames($shareTwo);
        $this->assertTrue(is_array($sharedEmpArray));
        $this->assertEquals(3, count($sharedEmpArray));

        $this->assertEquals(null, $sharedEmpArray[0]['employee_number']);
        $this->assertEquals("Admin", $sharedEmpArray[0]['employee_name']);
        $this->assertEquals("Administrator", $sharedEmpArray[0]['employee_job_title']);

        $this->assertEquals($employeeOne->getEmpNumber(), intval($sharedEmpArray[1]['employee_number']));
        $this->assertEquals($employeeOne->getFirstAndLastNames(), $sharedEmpArray[1]['employee_name']);
        $this->assertEquals($employeeOne->getJobTitleName(), $sharedEmpArray[1]['employee_job_title']);

        $this->assertEquals($employeeTwo->getEmpNumber(), intval($sharedEmpArray[2]['employee_number']));
        $this->assertEquals($employeeTwo->getFirstAndLastNames(), $sharedEmpArray[2]['employee_name']);
        $this->assertEquals($employeeTwo->getJobTitleName(), $sharedEmpArray[2]['employee_job_title']);
    }

    public function testGetSharedEmployeeNamesWhenPostedByEmployeeSharedByAdminAndEmployee() {

        $empIdOne = 1;
        $firstNameOne = 'James';
        $lastNameOne = 'White';

        $shareCollection = new Doctrine_Collection('Share');

        $jobTitleOne = new JobTitle();
        $jobTitleOne->setJobTitleName('CTO');

        $employeeOne = new Employee();
        $employeeOne->setEmpNumber($empIdOne);
        $employeeOne->setFirstName($firstNameOne);
        $employeeOne->setLastName($lastNameOne);
        $employeeOne->setJobTitle($jobTitleOne);

        $shareOne = new Share();
        $shareOne->setType(0);
        $shareCollection->add($shareOne);

        $shareTwo = new Share();
        $shareTwo->setType(1);
        $shareTwo->setEmployeePostShared($employeeOne);
        $shareCollection->add($shareTwo);

        $shareFour = new Share();
        $shareFour->setType(1);
        $shareCollection->add($shareFour);

        $post = new Post();
        $post->setEmployeePostAdded($employeeOne);
        $post->setShare($shareCollection);

        $shareOne->setPostShared($post);
        $shareTwo->setPostShared($post);
        $shareFour->setPostShared($post);

        $sharedEmpArray = $this->buzzService->getSharedEmployeeNames($shareTwo);
        $this->assertTrue(is_array($sharedEmpArray));
        $this->assertEquals(2, count($sharedEmpArray));

        $this->assertEquals(null, $sharedEmpArray[0]['employee_number']);
        $this->assertEquals("Admin", $sharedEmpArray[0]['employee_name']);
        $this->assertEquals("Administrator", $sharedEmpArray[0]['employee_job_title']);

        $this->assertEquals($employeeOne->getEmpNumber(), intval($sharedEmpArray[1]['employee_number']));
        $this->assertEquals($employeeOne->getFirstAndLastNames(), $sharedEmpArray[1]['employee_name']);
        $this->assertEquals($employeeOne->getJobTitleName(), $sharedEmpArray[1]['employee_job_title']);
    }

    public function testGetSharesFromEmployeeNumber() {
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getSharesFromEmployeeNumber'))
			->getMock();

        $shareOne = new Share();
        $shareOne->setId(1);
        $shareOne->setEmployeeNumber(1);

        $shareTwo = new Share();
        $shareTwo->setId(2);
        $shareTwo->setEmployeeNumber(1);
        $shareArray = array($shareOne, $shareTwo);
        

        $buzzDao->expects($this->once())
                ->method('getSharesFromEmployeeNumber')
                ->with(1)
                ->will($this->returnValue($shareArray));

        $this->buzzService->setBuzzDao($buzzDao);
        $resultShares = $this->buzzService->getSharesFromEmployeeNumber(1);
        $this->assertTrue(is_array($resultShares));
        $this->assertEquals(2, count($resultShares));
    }
    
    public function testGetSharesOfAdmin() {
        $buzzDao = $this->getMockBuilder('buzzDao')
			->setMethods( array('getSharesFromEmployeeNumber'))
			->getMock();

        $shareOne = new Share();
        $shareOne->setId(1);
        $shareOne->setEmployeeNumber(0);

        $shareTwo = new Share();
        $shareTwo->setId(2);
        $shareTwo->setEmployeeNumber(0);
        
        $shareArray = array($shareOne, $shareTwo);

        $buzzDao->expects($this->once())
                ->method('getSharesFromEmployeeNumber')
                ->with(0)
                ->will($this->returnValue($shareArray));

        $this->buzzService->setBuzzDao($buzzDao);
        $resultShares = $this->buzzService->getSharesFromEmployeeNumber(0);
        $this->assertTrue(is_array($resultShares));
        $this->assertEquals(2, count($resultShares));
    }


    public function testGetImageResponseWithCachingNotHavingImage() {

        $request = $this->getMockBuilder('sfWebRequest')->disableOriginalConstructor()->getMock();

        $response = $this->getMockBuilder('sfWebResponse')->disableOriginalConstructor()
            ->setMethods(array('setStatusCode','getStatusCode'))
            ->getMock();
        $response->expects($this->once())
            ->method('setStatusCode')
            ->with('404')
            ->will($this->returnValue(null));

        $response->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue('404'));


        $response = $this->buzzService->getImageResponseWithCaching(null, $request, $response);
        $this->assertEquals("404",$response->getStatusCode());
    }


    public function testGetImageResponseWithCachingHaveImageNotMatchingETag() {

        $imagePath = __DIR__ ."/orangehrm.jpg";


        $handle = fopen($imagePath, "r");
        $imageContent = fread($handle, filesize($imagePath));
        fclose($handle);

        $buzzPhoto = new Photo();
        $buzzPhoto->setPhoto($imageContent);
        $buzzPhoto->setFileType("image/jpeg");
        $buzzPhoto->setSize("7173");
        $buzzPhoto->setWidth("200");
        $buzzPhoto->setHeight("200");


        $request = $this->getMockBuilder('sfWebRequest')->disableOriginalConstructor()->setMethods(array('getHttpHeader'))->getMock();
        $request->expects($this->once())
            ->method('getHttpHeader')
            ->with('If-None-Match')
            ->will($this->returnValue(md5($imageContent)."xx"));

        $response = $this->getMockBuilder('sfWebResponse')->disableOriginalConstructor()
                    ->setMethods(array('setStatusCode','getStatusCode','setContentType', 'getContentType'))
                    ->getMock();
        $response->expects($this->never())
            ->method('setStatusCode')
            ->will($this->returnValue(null));

        $response->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue('200'));

        $response->expects($this->once())
            ->method('getContentType')
            ->will($this->returnValue('image/jpeg'));

        $response->expects($this->once())
            ->method('setContentType')
            ->with('image/jpeg')
            ->will($this->returnValue(null));

        $date = new DateTime();
        $response = $this->buzzService->getImageResponseWithCaching($buzzPhoto, $request, $response);
        $this->assertEquals("200", $response->getStatusCode());
        $this->assertEquals("image/jpeg", $response->getContentType());
        $this->assertEquals($imageContent, $response->getContent());
        $this->assertEquals("Public", $response->getHttpHeader("Pragma"));
        $this->assertEquals(md5($imageContent), $response->getHttpHeader("ETag"));
        $date->modify('+1 Year');
        $this->assertEquals(gmdate('D, d M Y H:i:s', $date->getTimestamp()) . ' GMT', $response->getHttpHeader("Expires"));
        $this->assertEquals("public, max-age=31536000, must-revalidate", $response->getHttpHeader("Cache-Control"));

    }


    public function testGetImageResponseWithCachingHaveImageMatchingETag() {

        $imagePath = __DIR__ ."/orangehrm.jpg";


        $handle = fopen($imagePath, "r");
        $imageContent = fread($handle, filesize($imagePath));
        fclose($handle);

        $buzzPhoto = new Photo();
        $buzzPhoto->setPhoto($imageContent);
        $buzzPhoto->setFileType("image/jpeg");
        $buzzPhoto->setSize("7173");
        $buzzPhoto->setWidth("200");
        $buzzPhoto->setHeight("200");


        $request = $this->getMockBuilder('sfWebRequest')->disableOriginalConstructor()->setMethods(array('getHttpHeader'))->getMock();
        $request->expects($this->once())
            ->method('getHttpHeader')
            ->with('If-None-Match')
            ->will($this->returnValue(md5($imageContent)));

        $response = $this->getMockBuilder('sfWebResponse')->disableOriginalConstructor()
            ->setMethods(array('setStatusCode','getStatusCode','setContentType', 'getContentType'))
            ->getMock();
        $response->expects($this->once())
            ->method('setStatusCode')
            ->with("304")
            ->will($this->returnValue(null));

        $response->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue('304'));

        $response->expects($this->once())
            ->method('getContentType')
            ->will($this->returnValue('text/html; charset=utf-8'));

        $date = new DateTime();
        $response = $this->buzzService->getImageResponseWithCaching($buzzPhoto, $request, $response);
        $this->assertEquals("304", $response->getStatusCode());
        $this->assertEquals("text/html; charset=utf-8", $response->getContentType());
        $this->assertEquals("", $response->getContent());
        $this->assertEquals("Public", $response->getHttpHeader("Pragma"));
        $this->assertEquals(md5($imageContent), $response->getHttpHeader("ETag"));
        $date->modify('+1 Year');
        $this->assertEquals(gmdate('D, d M Y H:i:s', $date->getTimestamp()) . ' GMT', $response->getHttpHeader("Expires"));
        $this->assertEquals("public, max-age=31536000, must-revalidate", $response->getHttpHeader("Cache-Control"));

    }

    public function testGetEmployeeImageResponseWithCachingNotHavingImage() {

        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmBuzzPlugin/test/fixtures/OrangeBuzz.yml';
        TestDataService::populate($this->fixture);

        $request = $this->getMockBuilder('sfWebRequest')->disableOriginalConstructor()->getMock();

        $response = $this->getMockBuilder('sfWebResponse')->disableOriginalConstructor()
            ->setMethods(array('getContentType','setContentType'))
            ->getMock();

        $response->expects($this->once())
            ->method('getContentType')
            ->will($this->returnValue('image/png'));

        $response->expects($this->once())
            ->method('setContentType')
            ->with('image/png')
            ->will($this->returnValue(null));

        $mockMyUser = $this->getMockBuilder('myUser')
                    ->disableOriginalConstructor()
                    ->setMethods(array('hasAttribute','getAttribute'))
                    ->getMock();

        $mockMyUser->expects($this->once())
            ->method('hasAttribute')
            ->with('meta.themeName')
            ->will($this->returnValue(true));

        $mockMyUser->expects($this->once())
            ->method('getAttribute')
            ->with('meta.themeName')
            ->will($this->returnValue('default'));


        $tmpName = ROOT_PATH . '/symfony/web/themes/default/images/default-photo.png';
        $fp = fopen($tmpName, 'r');
        $fileSize = filesize($tmpName);
        $contents = fread($fp, $fileSize);
        $contentType = "image/png";
        fclose($fp);


        $response = $this->buzzService->getEmployeeImageResponseWithCaching(null, $request, $response, $mockMyUser);
        $this->assertEquals("200",$response->getStatusCode());
        $this->assertEquals($contentType, $response->getContentType());
        $this->assertEquals($contents, $response->getContent());
        $this->assertEquals("Public", $response->getHttpHeader("Pragma"));
        $this->assertEquals(md5($contents), $response->getHttpHeader("ETag"));
        $this->assertEquals("public, max-age=0, must-revalidate", $response->getHttpHeader("Cache-Control"));

    }

    public function testGetEmployeeImageResponseWithCachingNotHavingImageThemeNotFetched() {

        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmBuzzPlugin/test/fixtures/OrangeBuzz.yml';
        TestDataService::populate($this->fixture);

        $request = $this->getMockBuilder('sfWebRequest')->disableOriginalConstructor()->getMock();

        $response = $this->getMockBuilder('sfWebResponse')->disableOriginalConstructor()
            ->setMethods(array('getContentType','setContentType'))
            ->getMock();

        $response->expects($this->once())
            ->method('getContentType')
            ->will($this->returnValue('image/png'));

        $response->expects($this->once())
            ->method('setContentType')
            ->with('image/png')
            ->will($this->returnValue(null));

        $mockMyUser = $this->getMockBuilder('myUser')
                    ->disableOriginalConstructor()
                    ->setMethods(array('hasAttribute','setAttribute','getAttribute'))
                    ->getMock();

        $mockMyUser->expects($this->once())
            ->method('hasAttribute')
            ->with('meta.themeName')
            ->will($this->returnValue(false));

        $mockMyUser->expects($this->once())
            ->method('setAttribute')
            ->with('meta.themeName','default',null)
            ->will($this->returnValue(null));

        $mockMyUser->expects($this->once())
            ->method('getAttribute')
            ->with('meta.themeName')
            ->will($this->returnValue('default'));


        $tmpName = ROOT_PATH . '/symfony/web/themes/default/images/default-photo.png';
        $fp = fopen($tmpName, 'r');
        $fileSize = filesize($tmpName);
        $contents = fread($fp, $fileSize);
        $contentType = "image/png";
        fclose($fp);


        $response = $this->buzzService->getEmployeeImageResponseWithCaching(null, $request, $response, $mockMyUser);
        $this->assertEquals("200",$response->getStatusCode());
        $this->assertEquals($contentType, $response->getContentType());
        $this->assertEquals($contents, $response->getContent());
        $this->assertEquals("Public", $response->getHttpHeader("Pragma"));
        $this->assertEquals(md5($contents), $response->getHttpHeader("ETag"));
        $this->assertEquals("public, max-age=0, must-revalidate", $response->getHttpHeader("Cache-Control"));

    }

    public function testGetEmployeeImageResponseWithImageWrongEtag() {

        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmBuzzPlugin/test/fixtures/OrangeBuzz.yml';
        TestDataService::populate($this->fixture);

        $tmpName = ROOT_PATH . '/symfony/web/themes/default/images/default-photo.png';
        $fp = fopen($tmpName, 'r');
        $fileSize = filesize($tmpName);
        $contents = fread($fp, $fileSize);
        $contentType = "image/png";
        fclose($fp);

        $request = $this->getMockBuilder('sfWebRequest')->disableOriginalConstructor()->setMethods(array("getHttpHeader"))->getMock();
        $request->expects($this->once())
            ->method('getHttpHeader')
            ->with('If-None-Match')
            ->will($this->returnValue(md5($contents)."xx"));

        $response = $this->getMockBuilder('sfWebResponse')->disableOriginalConstructor()
            ->setMethods(array('getContentType','setContentType'))
            ->getMock();

        $response->expects($this->once())
            ->method('getContentType')
            ->will($this->returnValue('image/png'));

        $response->expects($this->once())
            ->method('setContentType')
            ->with('image/png')
            ->will($this->returnValue(null));

        $mockMyUser = $this->getMockBuilder('myUser')
                    ->disableOriginalConstructor()
                    ->setMethods(array('hasAttribute','getAttribute'))
                    ->getMock();

        $mockMyUser->expects($this->never())
            ->method('hasAttribute')
            ->with('meta.themeName')
            ->will($this->returnValue(true));

        $mockMyUser->expects($this->never())
            ->method('getAttribute')
            ->with('meta.themeName')
            ->will($this->returnValue('default'));


        $empPicture = new EmpPicture();
        $empPicture->setFileType($contentType);
        $empPicture->setPicture($contents);

        $response = $this->buzzService->getEmployeeImageResponseWithCaching($empPicture, $request, $response, $mockMyUser);
        $this->assertEquals("200",$response->getStatusCode());
        $this->assertEquals($contentType, $response->getContentType());
        $this->assertEquals($contents, $response->getContent());
        $this->assertEquals("Public", $response->getHttpHeader("Pragma"));
        $this->assertEquals(md5($contents), $response->getHttpHeader("ETag"));
        $this->assertEquals("public, max-age=0, must-revalidate", $response->getHttpHeader("Cache-Control"));

    }



    public function testGetEmployeeImageResponseWithImageSameEtag() {

        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmBuzzPlugin/test/fixtures/OrangeBuzz.yml';
        TestDataService::populate($this->fixture);

        $tmpName = ROOT_PATH . '/symfony/web/themes/default/images/default-photo.png';
        $fp = fopen($tmpName, 'r');
        $fileSize = filesize($tmpName);
        $contents = fread($fp, $fileSize);
        $contentType = "image/png";
        fclose($fp);

        $request = $this->getMockBuilder('sfWebRequest')->disableOriginalConstructor()->setMethods(array("getHttpHeader"))->getMock();
        $request->expects($this->once())
            ->method('getHttpHeader')
            ->with('If-None-Match')
            ->will($this->returnValue(md5($contents)));

        $response = $this->getMockBuilder('sfWebResponse')->disableOriginalConstructor()
            ->setMethods(array('setContentType'))
            ->getMock();

        $response->expects($this->never())
            ->method('setContentType')
            ->with('image/png')
            ->will($this->returnValue(null));

        $mockMyUser = $this->getMockBuilder('myUser')
                    ->disableOriginalConstructor()
                    ->setMethods(array('hasAttribute','getAttribute'))
                    ->getMock();

        $mockMyUser->expects($this->never())
            ->method('hasAttribute')
            ->with('meta.themeName')
            ->will($this->returnValue(false));

        $mockMyUser->expects($this->never())
            ->method('getAttribute')
            ->with('meta.themeName')
            ->will($this->returnValue('default'));


        $empPicture = new EmpPicture();
        $empPicture->setFileType($contentType);
        $empPicture->setPicture($contents);

        $response = $this->buzzService->getEmployeeImageResponseWithCaching($empPicture, $request, $response, $mockMyUser);
        $this->assertEquals("304",$response->getStatusCode());
        $this->assertEquals(null, $response->getContentType());
        $this->assertEquals("", $response->getContent());
        $this->assertEquals("Public", $response->getHttpHeader("Pragma"));
        $this->assertEquals(md5($contents), $response->getHttpHeader("ETag"));
        $this->assertEquals("public, max-age=0, must-revalidate", $response->getHttpHeader("Cache-Control"));

    }

}

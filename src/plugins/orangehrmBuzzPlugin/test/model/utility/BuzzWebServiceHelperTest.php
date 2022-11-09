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
 * Description of BuzzWebServiceHelperUtilityTest
 *
 * @author nirmal
 * @group buzz
 */
class BuzzWebServiceHelperTest extends PHPUnit\Framework\TestCase {

    private $buzzWebServiceHelper;

    /**
     * Set up method
     */
    protected function setUp(): void {
        $this->buzzWebServiceHelper = new BuzzWebServiceHelper();
    }

    /**
     * @covers BuzzWebServiceHelper::getBuzzService
     */
    public function testGetBuzzService() {
        $buzzService = $this->buzzWebServiceHelper->getBuzzService();
        $this->assertTrue($buzzService instanceof BuzzService);
    }

    /**
     * @covers BuzzWebServiceHelper::getBuzzService
     */
    public function testGetBuzzObjectBuilder() {
        $buzzObjectBuilder = $this->buzzWebServiceHelper->getBuzzObjectBuilder();
        $this->assertTrue($buzzObjectBuilder instanceof BuzzObjectBuilder);
    }

    /**
     * @covers BuzzWebServiceHelper::getBuzzShares
     */
    public function testGetBuzzSharesWithLimit() {
        $shares = array(
            new share()
        );

        $shareArray = array(
            array()
        );

        $shareArray = array(
            array()
        );

        $limit = 1;

        $buzzServiceMock = $this->getMockBuilder('BuzzService')
			->setMethods( array('getShares'))
			->getMock();
        $buzzServiceMock->expects($this->once())
                ->method('getShares')
                ->with($limit)
                ->will($this->returnValue($shares));

        $buzzObjectBuilderMock = $this->getMockBuilder('BuzzObjectBuilder')
			->setMethods( array('getShareCollectionArray'))
			->getMock();
        $buzzObjectBuilderMock->expects($this->once())
                ->method('getShareCollectionArray')
                ->with($shares)
                ->will($this->returnValue($shareArray));

        $this->buzzWebServiceHelper->setBuzzService($buzzServiceMock);
        $this->buzzWebServiceHelper->setBuzzObjectBuilder($buzzObjectBuilderMock);

        $resultSharesArray = $this->buzzWebServiceHelper->getBuzzShares($limit);
        $this->assertEquals(1, count($resultSharesArray));
    }

    /**
     * @covers BuzzWebServiceHelper::getBuzzShares
     */
    public function testGetBuzzSharesWithoutLimit() {
        $shares = array(
            new share(),
            new share()
        );

        $shareArray = array(
            array(),
            array()
        );

        $buzzServiceMock = $this->getMockBuilder('BuzzService')
			->setMethods( array('getShares'))
			->getMock();
        $buzzServiceMock->expects($this->once())
                ->method('getShares')
                ->will($this->returnValue($shares));

        $buzzObjectBuilderMock = $this->getMockBuilder('BuzzObjectBuilder')
			->setMethods( array('getShareCollectionArray'))
			->getMock();
        $buzzObjectBuilderMock->expects($this->once())
                ->method('getShareCollectionArray')
                ->with($shares)
                ->will($this->returnValue($shareArray));

        $this->buzzWebServiceHelper->setBuzzService($buzzServiceMock);
        $this->buzzWebServiceHelper->setBuzzObjectBuilder($buzzObjectBuilderMock);

        $shareCollection = $this->buzzWebServiceHelper->getBuzzShares();
        $this->assertEquals(2, count($shareCollection));
    }

    /**
     * @covers BuzzWebServiceHelper::getLatestBuzzShares
     */
    public function testGetLatestBuzzShares() {
        $shares = array(
            new share(),
            new share()
        );

        $shareArray = array(
            array(),
            array()
        );

        $latestShareId = 1;

        $buzzServiceMock = $this->getMockBuilder('BuzzService')
			->setMethods( array('getSharesUptoId'))
			->getMock();
        $buzzServiceMock->expects($this->once())
                ->method('getSharesUptoId')
                ->will($this->returnValue($shares));

        $buzzObjectBuilderMock = $this->getMockBuilder('BuzzObjectBuilder')
			->setMethods( array('getShareCollectionArray'))
			->getMock();
        $buzzObjectBuilderMock->expects($this->once())
                ->method('getShareCollectionArray')
                ->with($shares)
                ->will($this->returnValue($shareArray));

        $this->buzzWebServiceHelper->setBuzzService($buzzServiceMock);
        $this->buzzWebServiceHelper->setBuzzObjectBuilder($buzzObjectBuilderMock);

        $shareCollection = $this->buzzWebServiceHelper->getLatestBuzzShares($latestShareId);
        $this->assertEquals(2, count($shareCollection));
    }

    
    /**
     * @covers BuzzWebServiceHelper::getMoreBuzzShares
     */
    public function testGetMoreBuzzSharesWithLimit() {
        $shares = array(
            new share()
        );

        $shareArray = array(
            array()
        );

        $lastShareId = 1;
        $limit = 1;

        $buzzServiceMock = $this->getMockBuilder('BuzzService')
			->setMethods( array('getMoreShares'))
			->getMock();
        $buzzServiceMock->expects($this->once())
                ->method('getMoreShares')
                ->with($limit)
                ->will($this->returnValue($shares));

        $buzzObjectBuilderMock = $this->getMockBuilder('BuzzObjectBuilder')
			->setMethods( array('getShareCollectionArray'))
			->getMock();
        $buzzObjectBuilderMock->expects($this->once())
                ->method('getShareCollectionArray')
                ->with($shares)
                ->will($this->returnValue($shareArray));

        $this->buzzWebServiceHelper->setBuzzService($buzzServiceMock);
        $this->buzzWebServiceHelper->setBuzzObjectBuilder($buzzObjectBuilderMock);

        $resultSharesArray = $this->buzzWebServiceHelper->getMoreBuzzShares($lastShareId, $limit);
        $this->assertEquals(1, count($resultSharesArray));
    }

    /**
     * @covers BuzzWebServiceHelper::getMoreBuzzShares
     */
    public function testGetMoreBuzzSharesWithoutLimit() {
        $shares = array(
            new share(),
            new share()
        );

        $shareArray = array(
            array(),
            array()
        );
        $lastShareId = 1;
        $limit = 1;

        $buzzServiceMock = $this->getMockBuilder('BuzzService')
			->setMethods( array('getMoreShares'))
			->getMock();
        $buzzServiceMock->expects($this->once())
                ->method('getMoreShares')
                ->will($this->returnValue($shares));

        $buzzObjectBuilderMock = $this->getMockBuilder('BuzzObjectBuilder')
			->setMethods( array('getShareCollectionArray'))
			->getMock();
        $buzzObjectBuilderMock->expects($this->once())
                ->method('getShareCollectionArray')
                ->with($shares)
                ->will($this->returnValue($shareArray));

        $this->buzzWebServiceHelper->setBuzzService($buzzServiceMock);
        $this->buzzWebServiceHelper->setBuzzObjectBuilder($buzzObjectBuilderMock);

        $shareCollection = $this->buzzWebServiceHelper->getMoreBuzzShares($lastShareId, $limit);
        $this->assertEquals(2, count($shareCollection));
    }

    /**
     * @covers BuzzWebServiceHelper::getShareAndPostDetailsByShareId
     */
    public function testGetShareAndPostDetailsByShareId() {
        $shareId = 1;
        $postId = 1;

        $post = new Post();
        $post->setId($postId);
        $post->setPostTime('2015-02-10 00:00:00');

        $share = new Share();
        $share->setId($shareId);
        $share->setPostShared($post);
        $share->setShareTime('2015-02-20 00:00:00');

        $postPhoto = new Photo();
        $postPhoto->setId(1);
        $postPhoto->setFilename('abc.jpg');

        $photos = array(
            $postPhoto
        );

        $shareDetailsArray = array(
            'share' => array('details' => $share->toArray()),
            'post' => array('details' => $post->toArray())
        );

        $shareDetailsArray = array(
            'share' => array('details' => $share->toArray()),
            'post' => array('details' => $post->toArray())
        );

        $buzzServiceMock = $this->getMockBuilder('BuzzService')
			->setMethods( array('getShareById', 'getPostPhotos'))
			->getMock();
        $buzzServiceMock->expects($this->once())
                ->method('getShareById')
                ->with($shareId)
                ->will($this->returnValue($share));
        $buzzServiceMock->expects($this->once())
                ->method('getPostPhotos')
                ->with($postId)
                ->will($this->returnValue($photos));

        $buzzObjectBuilderMock = $this->getMockBuilder('BuzzObjectBuilder')
			->setMethods( array('getShareDetailsAsArray'))
			->getMock();
        $buzzObjectBuilderMock->expects($this->once())
                ->method('getShareDetailsAsArray')
                ->with($share, $post, $photos)
                ->will($this->returnValue($shareDetailsArray));

        $buzzObjectBuilderMock = $this->getMockBuilder('BuzzObjectBuilder')
			->setMethods( array('getShareDetailsAsArray'))
			->getMock();
        $buzzObjectBuilderMock->expects($this->once())
                ->method('getShareDetailsAsArray')
                ->with($share, $post, $photos)
                ->will($this->returnValue($shareDetailsArray));

        $this->buzzWebServiceHelper->setBuzzService($buzzServiceMock);
        $this->buzzWebServiceHelper->setBuzzObjectBuilder($buzzObjectBuilderMock);

        $shareWithPostDetails = $this->buzzWebServiceHelper->getShareAndPostDetailsByShareId($shareId);
        $this->assertEquals('2015-02-20 00:00:00', $shareWithPostDetails['share']['details']['share_time']);
        $this->assertEquals('2015-02-10 00:00:00', $shareWithPostDetails['post']['details']['post_time']);
    }

    /**
     * @covers BuzzWebServiceHelper::postContentOnFeed
     */
    public function testPostContentOnFeedWithoutImages() {
        $shareId = 1;
        $postId = 1;

        $employeeNumber = 1;
        $content = 'Test content';
        $postAndShareDateTime = '2015-02-10 00:00:00';

        $post = new Post();
        $post->setId($postId);
        $post->setEmployeeNumber($employeeNumber);
        $post->setText($content);
        $post->setPostTime($postAndShareDateTime);

        $share = new Share();
        $share->setId($shareId);
        $share->setPostShared($post);
        $share->setEmployeeNumber($employeeNumber);
        $share->setShareTime($postAndShareDateTime);

        $buzzObjectBuilderMock = $this->getMockBuilder('BuzzObjectBuilder')
			->setMethods( array('createPost', 'createShare'))
			->getMock();
        $buzzObjectBuilderMock->expects($this->once())
                ->method('createPost')
                ->with($employeeNumber, $content, $postAndShareDateTime)
                ->will($this->returnValue($post));
        $buzzObjectBuilderMock->expects($this->once())
                ->method('createShare')
                ->with($post, $postAndShareDateTime)
                ->will($this->returnValue($share));

        $buzzServiceMock = $this->getMockBuilder('BuzzService')
			->setMethods( array('saveShare'))
			->getMock();
        $buzzServiceMock->expects($this->once())
                ->method('saveShare')
                ->will($this->returnValue($share));

        $this->buzzWebServiceHelper->setBuzzService($buzzServiceMock);
        $this->buzzWebServiceHelper->setBuzzObjectBuilder($buzzObjectBuilderMock);

        $returnShare = $this->buzzWebServiceHelper->postContentOnFeed($employeeNumber, $content, $postAndShareDateTime);
        $this->assertEquals($postAndShareDateTime, $returnShare->getShareTime());
    }

    /**
     * @covers BuzzWebServiceHelper::postContentOnFeed
     */
    public function testPostContentOnFeedWithImages() {
        $shareId = 1;
        $postId = 1;

        $imageDataArray = array(
            array(
                BuzzObjectBuilder::KEY_IMAGE_STRING_ENCODED => null,
                BuzzObjectBuilder::KEY_IMAGE_NAME => 'test_image',
                BuzzObjectBuilder::KEY_IMAGE_TYPE => 'jpg'
            )
        );

        $extraPostOptions = array(
            BuzzObjectBuilder::KEY_IMAGE_DATA => json_encode($imageDataArray)
        );

        $photo = new Photo();
        $photo->setFilename('test_image');
        $photo->setFileType('jpg');

        $imagesArray = array(
            $photo
        );

        $employeeNumber = 1;
        $content = 'Test content';
        $postAndShareDateTime = '2015-02-10 00:00:00';

        $post = new Post();
        $post->setId($postId);
        $post->setEmployeeNumber($employeeNumber);
        $post->setText($content);
        $post->setPostTime($postAndShareDateTime);

        $share = new Share();
        $share->setId($shareId);
        $share->setPostShared($post);
        $share->setEmployeeNumber($employeeNumber);
        $share->setShareTime($postAndShareDateTime);
        $share->setPostId($postId);

        $buzzObjectBuilderMock = $this->getMockBuilder('BuzzObjectBuilder')
			->setMethods( array('createPost', 'createShare', 'extractImagesForPost'))
			->getMock();
        $buzzObjectBuilderMock->expects($this->once())
                ->method('createPost')
                ->with($employeeNumber, $content, $postAndShareDateTime)
                ->will($this->returnValue($post));
        $buzzObjectBuilderMock->expects($this->once())
                ->method('createShare')
                ->with($post, $postAndShareDateTime)
                ->will($this->returnValue($share));
        $buzzObjectBuilderMock->expects($this->once())
                ->method('extractImagesForPost')
                ->with($extraPostOptions, $share->getPostId())
                ->will($this->returnValue($imagesArray));

        $buzzServiceMock = $this->getMockBuilder('BuzzService')
			->setMethods( array('saveShare', 'savePhoto'))
			->getMock();
        $buzzServiceMock->expects($this->once())
                ->method('saveShare')
                ->will($this->returnValue($share));
        $buzzServiceMock->expects($this->once())
                ->method('savePhoto')
                ->with($photo)
                ->will($this->returnValue($photo));

        $this->buzzWebServiceHelper->setBuzzService($buzzServiceMock);
        $this->buzzWebServiceHelper->setBuzzObjectBuilder($buzzObjectBuilderMock);

        $returnShare = $this->buzzWebServiceHelper->postContentOnFeed($employeeNumber, $content, $postAndShareDateTime, $extraPostOptions);
        $this->assertEquals($postAndShareDateTime, $returnShare->getShareTime());
    }

    /**
     * @covers BuzzWebServiceHelper::commentOnShare
     */
    public function testCommentOnShare() {
        $employeeNumber = 1;
        $content = 'Test content';
        $postAndShareDateTime = '2015-02-10 00:00:00';
        $shareId = 1;

        $comment = new Comment();
        $comment->setShareId($shareId);
        $comment->setEmployeeNumber($employeeNumber);
        $comment->setCommentText($content);
        $comment->setCommentTime($postAndShareDateTime);
        $comment->setNumberOfLikes(0);
        $comment->setNumberOfUnlikes(0);

        $buzzObjectBuilderMock = $this->getMockBuilder('BuzzObjectBuilder')
			->setMethods( array('createCommentOnShare'))
			->getMock();
        $buzzObjectBuilderMock->expects($this->once())
                ->method('createCommentOnShare')
                ->with($shareId, $employeeNumber, $content, $postAndShareDateTime)
                ->will($this->returnValue($comment));

        $buzzServiceMock = $this->getMockBuilder('BuzzService')
			->setMethods( array('saveCommentShare'))
			->getMock();
        $buzzServiceMock->expects($this->once())
                ->method('saveCommentShare')
                ->will($this->returnValue($comment));

        $this->buzzWebServiceHelper->setBuzzService($buzzServiceMock);
        $this->buzzWebServiceHelper->setBuzzObjectBuilder($buzzObjectBuilderMock);

        $returnComment = $this->buzzWebServiceHelper->commentOnShare($shareId, $employeeNumber, $content, $postAndShareDateTime);
        $this->assertEquals($postAndShareDateTime, $returnComment->getCommentTime());
    }

    /**
     * This method tests the scenario where an employee who has disliked before liking now on a share
     * @covers BuzzWebServiceHelper::likeOnShare
     */
    public function testLikeOnShare() {
        $employeeNumber = 1;
        $testDateTime = '2015-02-10 00:00:00';
        $shareId = 1;

        $likeOnShare = new LikeOnShare();
        $likeOnShare->setShareId($shareId);
        $likeOnShare->setEmployeeNumber($employeeNumber);
        $likeOnShare->setLikeTime($testDateTime);

        $dislikeOnShare = new UnLikeOnShare();
        $dislikeOnShare->setShareId($shareId);
        $dislikeOnShare->setEmployeeNumber($employeeNumber);
        $dislikeOnShare->setLikeTime($testDateTime);

        $likesOnShare = new Doctrine_Collection('LikeOnShare');
        $likesOnShare->add($likeOnShare);

        $dislikesOnShare = new Doctrine_Collection('UnLikeOnShare');
        $dislikesOnShare->add($dislikeOnShare);

        $share = new Share();
        $share->setUnlike($dislikesOnShare);
        $share->setNumberOfUnlikes(1);
        $share->setNumberOfLikes(0);

        $shareUpdated = new Share();
        $shareUpdated->setLike($likesOnShare);
        $shareUpdated->setNumberOfUnlikes(0);
        $shareUpdated->setNumberOfLikes(1);

        $buzzObjectBuilderMock = $this->getMockBuilder('BuzzObjectBuilder')
			->setMethods( array('createLikeOnShare', 'createDislikeOnShare'))
			->getMock();
        $buzzObjectBuilderMock->expects($this->once())
                ->method('createLikeOnShare')
                ->with($shareId, $employeeNumber, $testDateTime)
                ->will($this->returnValue($likeOnShare));
        $buzzObjectBuilderMock->expects($this->once())
                ->method('createDislikeOnShare')
                ->with($shareId, $employeeNumber, $testDateTime)
                ->will($this->returnValue($dislikeOnShare));

        $buzzServiceMock = $this->getMockBuilder('BuzzService')
			->setMethods( array('getShareById', 'deleteUnLikeForShare', 'saveLikeForShare'))
			->getMock();
        $buzzServiceMock->expects($this->any())
                ->method('getShareById')
                ->with($shareId)
                ->will($this->onConsecutiveCalls($share, $shareUpdated));
        $buzzServiceMock->expects($this->any())
                ->method('deleteUnLikeForShare')
                ->with($dislikeOnShare)
                ->will($this->returnValue(1));
        $buzzServiceMock->expects($this->any())
                ->method('saveLikeForShare')
                ->with($likeOnShare)
                ->will($this->returnValue($likeOnShare));

        $this->buzzWebServiceHelper->setBuzzService($buzzServiceMock);
        $this->buzzWebServiceHelper->setBuzzObjectBuilder($buzzObjectBuilderMock);

        $resultShare = $this->buzzWebServiceHelper->likeOnShare($shareId, $employeeNumber, $testDateTime);
        $this->assertTrue($resultShare instanceof Share);
        $this->assertEquals(1, $resultShare->getNumberOfLikes());
    }

    /**
     * This method tests the scenario where an employee who has liked before disliking now on a share
     * @covers BuzzWebServiceHelper::dislikeOnShare
     */
    public function testDislikeOnShare() {
        $employeeNumber = 1;
        $testDateTime = '2015-02-10 00:00:00';
        $shareId = 1;

        $likeOnShare = new LikeOnShare();
        $likeOnShare->setShareId($shareId);
        $likeOnShare->setEmployeeNumber($employeeNumber);
        $likeOnShare->setLikeTime($testDateTime);

        $dislikeOnShare = new UnLikeOnShare();
        $dislikeOnShare->setShareId($shareId);
        $dislikeOnShare->setEmployeeNumber($employeeNumber);
        $dislikeOnShare->setLikeTime($testDateTime);

        $likesOnShare = new Doctrine_Collection('LikeOnShare');
        $likesOnShare->add($likeOnShare);

        $dislikesOnShare = new Doctrine_Collection('UnLikeOnShare');
        $dislikesOnShare->add($dislikeOnShare);

        $share = new Share();
        $share->setLike($likesOnShare);
        $share->setNumberOfUnlikes(0);
        $share->setNumberOfLikes(1);

        $shareUpdated = new Share();
        $shareUpdated->setUnlike($dislikesOnShare);
        $shareUpdated->setNumberOfUnlikes(1);
        $shareUpdated->setNumberOfLikes(0);

        $buzzObjectBuilderMock = $this->getMockBuilder('BuzzObjectBuilder')
			->setMethods( array('createLikeOnShare', 'createDislikeOnShare'))
			->getMock();
        $buzzObjectBuilderMock->expects($this->once())
                ->method('createLikeOnShare')
                ->with($shareId, $employeeNumber, $testDateTime)
                ->will($this->returnValue($likeOnShare));
        $buzzObjectBuilderMock->expects($this->once())
                ->method('createDislikeOnShare')
                ->with($shareId, $employeeNumber, $testDateTime)
                ->will($this->returnValue($dislikeOnShare));

        $buzzServiceMock = $this->getMockBuilder('BuzzService')
			->setMethods( array('getShareById', 'deleteLikeForShare', 'saveUnLikeForShare'))
			->getMock();
        $buzzServiceMock->expects($this->any())
                ->method('getShareById')
                ->with($shareId)
                ->will($this->onConsecutiveCalls($share, $shareUpdated));
        $buzzServiceMock->expects($this->any())
                ->method('deleteLikeForShare')
                ->with($likeOnShare)
                ->will($this->returnValue(1));
        $buzzServiceMock->expects($this->any())
                ->method('saveUnLikeForShare')
                ->with($dislikeOnShare)
                ->will($this->returnValue($dislikeOnShare));

        $this->buzzWebServiceHelper->setBuzzService($buzzServiceMock);
        $this->buzzWebServiceHelper->setBuzzObjectBuilder($buzzObjectBuilderMock);

        $resultShare = $this->buzzWebServiceHelper->dislikeOnShare($shareId, $employeeNumber, $testDateTime);
        $this->assertTrue($resultShare instanceof Share);
        $this->assertEquals(1, $resultShare->getNumberOfUnlikes());
    }

    /**
     * This method tests the scenario where an employee who has disliked before liking now on a comment
     * @covers BuzzWebServiceHelper::likeOnComment
     */
    public function testLikeOnComment() {
        $employeeNumber = 1;
        $testDateTime = '2015-02-10 00:00:00';
        $commentId = 1;

        $likeOnComment = new LikeOnComment();
        $likeOnComment->setCommentId($commentId);
        $likeOnComment->setEmployeeNumber($employeeNumber);
        $likeOnComment->setLikeTime($testDateTime);

        $dislikeOnComment = new UnLikeOnComment();
        $dislikeOnComment->setCommentId($commentId);
        $dislikeOnComment->setEmployeeNumber($employeeNumber);
        $dislikeOnComment->setLikeTime($testDateTime);

        $likesOnComment = new Doctrine_Collection('LikeOnComment');
        $likesOnComment->add($likeOnComment);

        $dislikesOnComment = new Doctrine_Collection('UnLikeOnComment');
        $dislikesOnComment->add($dislikeOnComment);

        $comment = new Comment();
        $comment->setUnlike($dislikesOnComment);
        $comment->setNumberOfUnlikes(1);
        $comment->setNumberOfLikes(0);

        $commentUpdated = new Comment();
        $commentUpdated->setLike($likesOnComment);
        $commentUpdated->setNumberOfUnlikes(0);
        $commentUpdated->setNumberOfLikes(1);

        $buzzObjectBuilderMock = $this->getMockBuilder('BuzzObjectBuilder')
			->setMethods( array('createLikeOnComment', 'createDislikeOnComment'))
			->getMock();
        $buzzObjectBuilderMock->expects($this->once())
                ->method('createLikeOnComment')
                ->with($commentId, $employeeNumber, $testDateTime)
                ->will($this->returnValue($likeOnComment));
        $buzzObjectBuilderMock->expects($this->once())
                ->method('createDislikeOnComment')
                ->with($commentId, $employeeNumber, $testDateTime)
                ->will($this->returnValue($dislikeOnComment));

        $buzzServiceMock = $this->getMockBuilder('BuzzService')
			->setMethods( array('getCommentById', 'deleteUnLikeForComment', 'saveLikeForComment'))
			->getMock();
        $buzzServiceMock->expects($this->any())
                ->method('getCommentById')
                ->with($commentId)
                ->will($this->onConsecutiveCalls($comment, $commentUpdated));
        $buzzServiceMock->expects($this->once())
                ->method('deleteUnLikeForComment')
                ->with($dislikeOnComment)
                ->will($this->returnValue(1));
        $buzzServiceMock->expects($this->once())
                ->method('saveLikeForComment')
                ->with($likeOnComment)
                ->will($this->returnValue($likeOnComment));

        $this->buzzWebServiceHelper->setBuzzService($buzzServiceMock);
        $this->buzzWebServiceHelper->setBuzzObjectBuilder($buzzObjectBuilderMock);

        $resultComment = $this->buzzWebServiceHelper->likeOnComment($commentId, $employeeNumber, $testDateTime);
        $this->assertTrue($resultComment instanceof Comment);
        $this->assertEquals(1, $resultComment->getNumberOfLikes());
    }

    /**
     * This method tests the scenario where an employee who has liked before disliking now on a comment 
     * @covers BuzzWebServiceHelper::dislikeOnComment
     */
    public function testDislikeOnComment() {
        $employeeNumber = 1;
        $testDateTime = '2015-02-10 00:00:00';
        $commentId = 1;

        $likeOnComment = new LikeOnComment();
        $likeOnComment->setCommentId($commentId);
        $likeOnComment->setEmployeeNumber($employeeNumber);
        $likeOnComment->setLikeTime($testDateTime);

        $dislikeOnComment = new UnLikeOnComment();
        $dislikeOnComment->setCommentId($commentId);
        $dislikeOnComment->setEmployeeNumber($employeeNumber);
        $dislikeOnComment->setLikeTime($testDateTime);

        $likesOnComment = new Doctrine_Collection('LikeOnComment');
        $likesOnComment->add($likeOnComment);

        $dislikesOnComment = new Doctrine_Collection('UnLikeOnComment');
        $dislikesOnComment->add($dislikeOnComment);

        $comment = new Comment();
        $comment->setLike($likesOnComment);
        $comment->setNumberOfUnlikes(0);
        $comment->setNumberOfLikes(1);

        $commentUpdated = new Comment();
        $commentUpdated->setUnlike($dislikesOnComment);
        $commentUpdated->setNumberOfUnlikes(1);
        $commentUpdated->setNumberOfLikes(0);

        $buzzObjectBuilderMock = $this->getMockBuilder('BuzzObjectBuilder')
			->setMethods( array('createLikeOnComment', 'createDislikeOnComment'))
			->getMock();
        $buzzObjectBuilderMock->expects($this->once())
                ->method('createLikeOnComment')
                ->with($commentId, $employeeNumber, $testDateTime)
                ->will($this->returnValue($likeOnComment));
        $buzzObjectBuilderMock->expects($this->once())
                ->method('createDislikeOnComment')
                ->with($commentId, $employeeNumber, $testDateTime)
                ->will($this->returnValue($dislikeOnComment));

        $buzzServiceMock = $this->getMockBuilder('BuzzService')
			->setMethods( array('getCommentById', 'deleteLikeForComment', 'saveUnLikeForComment'))
			->getMock();
        $buzzServiceMock->expects($this->any())
                ->method('getCommentById')
                ->with($commentId)
                ->will($this->onConsecutiveCalls($comment, $commentUpdated));
        $buzzServiceMock->expects($this->once())
                ->method('deleteLikeForComment')
                ->with($likeOnComment)
                ->will($this->returnValue(1));
        $buzzServiceMock->expects($this->once())
                ->method('saveUnLikeForComment')
                ->with($dislikeOnComment)
                ->will($this->returnValue($dislikeOnComment));

        $this->buzzWebServiceHelper->setBuzzService($buzzServiceMock);
        $this->buzzWebServiceHelper->setBuzzObjectBuilder($buzzObjectBuilderMock);

        $resultComment = $this->buzzWebServiceHelper->dislikeOnComment($commentId, $employeeNumber, $testDateTime);
        $this->assertTrue($resultComment instanceof Comment);
        $this->assertEquals(1, $resultComment->getNumberOfUnlikes());
    }
    
    public function testGetBuzzForEmployee() {
        $empNum = 1;
                
        $shareOne = new Share();
        $shareOne->setId(1);
        $shareOne->setEmployeeNumber($empNum);

        $shareTwo = new Share();
        $shareTwo->setId(2);
        $shareTwo->setEmployeeNumber($empNum);
        $shareArray = array($shareOne, $shareTwo);
        
        $buzzServiceMock = $this->getMockBuilder('BuzzService')
			->setMethods( array('getSharesFromEmployeeNumber'))
			->getMock();
        $buzzServiceMock->expects($this->once())
                ->method('getSharesFromEmployeeNumber')
                ->with($empNum)
                ->will($this->returnValue($shareArray));

        $buzzObjectBuilderMock = $this->getMockBuilder('BuzzObjectBuilder')
			->setMethods( array('getShareCollectionArray'))
			->getMock();
        $buzzObjectBuilderMock->expects($this->any())
                ->method('getShareCollectionArray')
                ->with($shareArray)
                ->will($this->returnValue($shareArray));

        $this->buzzWebServiceHelper->setBuzzService($buzzServiceMock);
        $this->buzzWebServiceHelper->setBuzzObjectBuilder($buzzObjectBuilderMock);

        $returnShareCollection = $this->buzzWebServiceHelper->getBuzzForEmployee($empNum, 1);
        $this->assertEquals(2, count($returnShareCollection));
        $this->assertTrue(is_array($returnShareCollection));
    }

    public function testDeleteShareWithExistingShareId() {
        $shareId = 1;
        $loggedInEmployeeNumber = 1;

        $share = new Share();
        $share->setId($shareId);
        $share->setEmployeeNumber($loggedInEmployeeNumber);

        $mockBuzzService = $this->getMockBuilder('buzzService')
			->setMethods( array('getShareById', 'deleteShare'))
			->getMock();
        $mockBuzzService->expects($this->once())
                ->method('getShareById')
                ->with($shareId)
                ->will($this->returnValue($share));

        $mockBuzzService->expects($this->once())
                ->method('deleteShare')
                ->with($shareId)
                ->will($this->returnValue(1));
        $this->buzzWebServiceHelper->setBuzzService($mockBuzzService);

        $responseArray = $this->buzzWebServiceHelper->deleteShare($shareId, $loggedInEmployeeNumber);

        $this->assertTrue(is_array($responseArray));
        $this->assertTrue($responseArray['success']);
    }

    public function testDeleteShareWithNonExistingShareId() {
        $shareId = 1;
        $loggedInEmployeeNumber = 1;

        $mockBuzzService = $this->getMockBuilder('buzzService')
			->setMethods( array('getShareById'))
			->getMock();
        $mockBuzzService->expects($this->once())
                ->method('getShareById')
                ->with($shareId)
                ->will($this->returnValue(false));
        $this->buzzWebServiceHelper->setBuzzService($mockBuzzService);

        $responseArray = $this->buzzWebServiceHelper->deleteShare($shareId, $loggedInEmployeeNumber);
        $this->assertTrue(is_array($responseArray));
        $this->assertFalse($responseArray['success']);
    }

    public function testDeleteShareWithExistingShareIdThatDoNotBelongToCurrentEmployee() {
        $shareId = 1;
        $loggedInEmployeeNumber = 1;
        $share = new Share();
        $share->setId($shareId);
        $share->setEmployeeNumber(12);

        $mockBuzzService = $this->getMockBuilder('buzzService')
			->setMethods( array('getShareById'))
			->getMock();
        $mockBuzzService->expects($this->once())
                ->method('getShareById')
                ->with($shareId)
                ->will($this->returnValue($share));
        $this->buzzWebServiceHelper->setBuzzService($mockBuzzService);

        $responseArray = $this->buzzWebServiceHelper->deleteShare($shareId, $loggedInEmployeeNumber);
        $this->assertTrue(is_array($responseArray));
        $this->assertFalse($responseArray['success']);
    }

    public function testDeleteCommentWithExistingCommentId() {
        $commentId = 1;
        $loggedInEmployeeNumber = 1;

        $comment = new Comment();
        $comment->setId($commentId);
        $comment->setEmployeeNumber($loggedInEmployeeNumber);

        $mockBuzzService = $this->getMockBuilder('buzzService')
			->setMethods( array('getCommentById', 'deleteCommentForShare'))
			->getMock();
        $mockBuzzService->expects($this->once())
                ->method('getCommentById')
                ->with($commentId)
                ->will($this->returnValue($comment));

        $mockBuzzService->expects($this->once())
                ->method('deleteCommentForShare')
                ->with($comment)
                ->will($this->returnValue(1));
        $this->buzzWebServiceHelper->setBuzzService($mockBuzzService);

        $responseArray = $this->buzzWebServiceHelper->deleteCommentForShare($commentId, $loggedInEmployeeNumber);

        $this->assertTrue(is_array($responseArray));
        $this->assertTrue($responseArray['success']);
    }

    public function testDeleteCommentWithNonExistingComentId() {
        $commentId = 1;
        $loggedInEmployeeNumber = 1;

        $mockBuzzService = $this->getMockBuilder('buzzService')
			->setMethods( array('getCommentById'))
			->getMock();
        $mockBuzzService->expects($this->once())
                ->method('getCommentById')
                ->with($commentId)
                ->will($this->returnValue(false));
        $this->buzzWebServiceHelper->setBuzzService($mockBuzzService);

        $responseArray = $this->buzzWebServiceHelper->deleteCommentForShare($commentId, $loggedInEmployeeNumber);
        $this->assertTrue(is_array($responseArray));
        $this->assertFalse($responseArray['success']);
    }

    public function testDeleteCommentWithExistingComentIdThatDoNotBelongToCurrentEmployee() {
        $commentId = 1;
        $loggedInEmployeeNumber = 1;

        $comment = new Comment();
        $comment->setId($commentId);
        $comment->setEmployeeNumber(14);

        $mockBuzzService = $this->getMockBuilder('buzzService')
			->setMethods( array('getCommentById'))
			->getMock();
        $mockBuzzService->expects($this->once())
                ->method('getCommentById')
                ->with($commentId)
                ->will($this->returnValue($comment));

        $this->buzzWebServiceHelper->setBuzzService($mockBuzzService);

        $responseArray = $this->buzzWebServiceHelper->deleteCommentForShare($commentId, $loggedInEmployeeNumber);
        $this->assertTrue(is_array($responseArray));
        $this->assertFalse($responseArray['success']);
    }

}

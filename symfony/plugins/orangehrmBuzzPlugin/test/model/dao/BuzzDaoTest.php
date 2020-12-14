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
class BuzzDaoTest extends PHPUnit\Framework\TestCase {

    private $buzzDao;

    /**
     * Set up method
     */
    protected function setUp(): void {
        $this->buzzDao = new BuzzDao();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmBuzzPlugin/test/fixtures/OrangeBuzz.yml';
        TestDataService::populate($this->fixture);
    }

    /**
     * test save post to the database
     */
    public function testGetSharesCount() {

        $resultShareCount = $this->buzzDao->getSharesCount();
        $this->assertEquals(5, $resultShareCount);
    }

    /**
     * test save post to the database
     */
    public function testSavePost() {
        $post = New Post();
        $post->setEmployeeNumber(1);
        $post->setText('this is test Post');
        $post->setPostTime('2015-01-10 12:12:12');
        $resultPost = $this->buzzDao->savePost($post);

        $this->assertTrue($resultPost instanceof Post);
        $this->assertTrue($resultPost->getId() != null);
        $this->assertEquals('2015-01-10 12:12:12', $resultPost->getPostTime());
    }

    /**
     * test save link to the database
     */
    public function testSaveLink() {
        $link = New Link();
        $link->setLink('fdfdfdd.com');
        $link->setPostId(2);
        $link->setDescription('description');
        $resultLink = $this->buzzDao->saveLink($link);

        $this->assertTrue($resultLink instanceof Link);
        $this->assertTrue($resultLink->getId() != null);
    }

    /**
     * this is function to test get shares from database
     */
    public function testGetShares() {
        $resultShares = $this->buzzDao->getShares(2);

        $this->assertEquals(2, count($resultShares));
        $this->assertTrue($resultShares->getFirst() instanceof Share);
    }

    /**
     * this is function to test get shares from database
     */
    public function testGetSharesOverLimit() {
        $limit = 50;
        $resultShares = $this->buzzDao->getShares($limit);

        $this->assertEquals(5, count($resultShares));
        $this->assertTrue($resultShares->getFirst() instanceof Share);
    }

    /**
     * this is function to test get the post from the share
     */
    public function testGetPostFromShare() {
        $resultShares = $this->buzzDao->getShares(2);

        $this->assertTrue($resultShares->getFirst()->getPostShared() instanceof Post);
    }

    /**
     * this is function to test delete post from database
     */
    public function testDeletePost() {
        $resultDeleteCount = $this->buzzDao->deletePost(1);

        $this->assertEquals(1, $resultDeleteCount);
    }

    /**
     * this is function to test delete post from database
     */
    public function testDeletePostWithIncorrectId() {
        $resultDeleteCount = $this->buzzDao->deletePost(100);

        $this->assertEquals(0, $resultDeleteCount);
    }

    public function testDeleteCommentOnShareSqlInjection() {
        $comment = new Comment();
        $comment->setId("1; delete from hs_hr_employee;");
        $comment->setShareId(1);
        $comment->setEmployeeNumber(1);
        $comment->setCommentTime('2015-01-10 12:12:12');
        $comment->setCommentText('this is the first comment');

        // Will generate SQL error if not properly escaped
        $resultDeleteCount = $this->buzzDao->deleteCommentForShare($comment);
        $this->assertEquals(1, $resultDeleteCount);
    }

    /** \
     * this is function to test delete share from database
     */
    public function testDeleteShare() {
        $resultDeleteCount = $this->buzzDao->deleteShare(1);

        $this->assertEquals(1, $resultDeleteCount);
    }

    /** \
     * this is function to test delete share from database
     */
    public function testDeleteShareWithIncorrectId() {
        $resultDeleteCount = $this->buzzDao->deleteShare(100);

        $this->assertEquals(0, $resultDeleteCount);
    }

    /**
     * this is function to test save like on share to database
     */
    public function testLikeOnShare() {
        $likeOnShare = new LikeOnShare();
        $likeOnShare->setShareId(1);
        $likeOnShare->setEmployeeNumber(1);
        $likeOnShare->setLikeTime('2015-01-10 12:12:12');

        $resultLike = $this->buzzDao->saveLikeForShare($likeOnShare);
        $this->assertTrue($resultLike->getId() != null);
    }

    /**
     * this is function to test save like on share to database
     */
    public function testLikeOnShareByAdmin() {
        $likeOnShare = new LikeOnShare();
        $likeOnShare->setShareId(1);
        $likeOnShare->setLikeTime('2015-01-10 12:12:12');

        $resultLike = $this->buzzDao->saveLikeForShare($likeOnShare);
        $this->assertTrue($resultLike->getId() != null);
    }

    /**
     * this is function to test delete like on share saving to database 
     */
    public function testDeleteLikeOnshare() {
        $likeOnShare = new LikeOnShare();
        $likeOnShare->setId(20);
        $likeOnShare->setShareId(1);
        $likeOnShare->setEmployeeNumber(1);
        $likeOnShare->setLikeTime('2015-01-10 12:12:12');

        $resultDeletCount = $this->buzzDao->deleteLikeForShare($likeOnShare);
        $this->assertEquals(1, $resultDeletCount);
    }

    /**
     * test deleting admin like on share
     */
    public function testDeleteLikeOnshareByAdmin() {
        $likeOnShare = new LikeOnShare();
        $likeOnShare->setId(20);
        $likeOnShare->setShareId(1);
        $likeOnShare->setLikeTime('2015-01-10 12:12:12');

        $resultDeletCount = $this->buzzDao->deleteLikeForShare($likeOnShare);
        $this->assertEquals(1, $resultDeletCount);
    }

    /**
     *  test save comment on share to database
     */
    public function testCommentOnShare() {
        $comment = new Comment();
        $comment->setShareId(1);
        $comment->setEmployeeNumber(1);
        $comment->setCommentTime('2015-01-10 12:12:12');
        $comment->setCommentText('this is the first comment');

        $resultComment = $this->buzzDao->saveCommentShare($comment);
        $this->assertTrue($resultComment->getId() != null);
        $this->assertEquals('2015-01-10 12:12:12', $resultComment->getCommentTime());
    }

    /**
     *  test delete comment onshare
     */
    public function testDeleteCommentOnShare() {
        $comment = new Comment();
        $comment->setId(1);
        $comment->setShareId(1);
        $comment->setEmployeeNumber(1);
        $comment->setCommentTime('2015-01-10 12:12:12');
        $comment->setCommentText('this is the first comment');

        $resultDeletCount = $this->buzzDao->deleteCommentForShare($comment);
        $this->assertEquals(1, $resultDeletCount);
    }

    /**
     * this is function to test save likes on comment
     */
    public function testLikeOnComment() {
        $likeOnComment = new LikeOnComment();
        $likeOnComment->setCommentId(1);
        $likeOnComment->setEmployeeNumber(1);
        $likeOnComment->setLikeTime('2015-01-10 12:12:12');

        $resultLikeOnComment = $this->buzzDao->saveLikeForComment($likeOnComment);
        $this->assertTrue($resultLikeOnComment->getId() != null);
        $this->assertEquals('2015-01-10 12:12:12', $resultLikeOnComment->getLikeTime());
    }

    /**
     * this is function to test save deletion of like on comment to database
     */
    public function testDeletLikeOnComment() {
        $likeOnComment = new LikeOnComment();
        $likeOnComment->setId(20);
        $likeOnComment->setCommentId(1);
        $likeOnComment->setEmployeeNumber(1);
        $likeOnComment->setLikeTime('2015-01-10 12:12:12');

        $resultDeletCount = $this->buzzDao->deleteLikeForComment($likeOnComment);
        $this->assertEquals(1, $resultDeletCount);
    }

    /**
     * this is function to test save deletion of like on comment to database
     */
    public function testDeletLikeOnCommentByAdmin() {
        $likeOnComment = new LikeOnComment();
        $likeOnComment->setId(20);
        $likeOnComment->setCommentId(1);
        $likeOnComment->setLikeTime('2015-01-10 12:12:12');

        $resultDeletCount = $this->buzzDao->deleteLikeForComment($likeOnComment);
        $this->assertEquals(1, $resultDeletCount);
    }

    /**
     * this is function to test updates in the database
     */
    public function testUpdate() {
        $result = $this->buzzDao->getShares(2);
        $share = $result->getFirst();
        $share->setText($share->getId() . ' this is updated one');
        $resultShare = $this->buzzDao->saveShare($share);

        $this->assertEquals($share->getId() . ' this is updated one', $resultShare->getText());
    }

    /**
     * this is functoin to test get share by id
     */
    public function testGetShareById() {
        $share = $this->buzzDao->getShareById(1);

        $this->assertTrue($share instanceof Share);
        $this->assertEquals(1, $share->getId());
    }

    /**
     * this is functoin to test get share by id
     */
    public function testGetShareByIdWithIncorrectId() {

        $share = $this->buzzDao->getShareById(100);

        $this->assertTrue($share == null);
    }

    /**
     * this is functoin to test get post by id
     */
    public function testGetPostById() {
        $post = $this->buzzDao->getPostById(1);

        $this->assertTrue($post instanceof Post);
        $this->assertEquals(1, $post->getId());
    }

    /**
     * this is functoin to test get post by id
     */
    public function testGetPostByIdWithIncorrectId() {
        $post = $this->buzzDao->getPostById(100);

        $this->assertTrue($post == null);
    }

    /**
     * this is functoin to test get Comment by id
     */
    public function testGetCommentById() {
        $comment = $this->buzzDao->getCommentById(1);

        $this->assertTrue($comment instanceof Comment);
        $this->assertEquals(1, $comment->getId());
    }

    /**
     * this is functoin to test get Comment by id
     */
    public function testGetCommentByIdWithIncorrectId() {
        $comment = $this->buzzDao->getCommentById(100);

        $this->assertTrue($comment == null);
    }

    /**
     * this is functoin to test get likeOnComment by id
     */
    public function testGetLikeOnCommentById() {
        $likeOnComment = $this->buzzDao->getLikeOnCommentById(21);
        $this->assertTrue($likeOnComment instanceof LikeOnComment);
        $this->assertEquals(21, $likeOnComment->getId());
    }

    /**
     * this is functoin to test get likeOnComment by id
     */
    public function testGetLikeOnCommentByIdWithIncorrectId() {
        $likeOnComment = $this->buzzDao->getLikeOnCommentById(210);
        $this->assertTrue($likeOnComment == null);
    }

    /**
     * this is functoin to test get likeOnShare by id
     */
    public function testGetLikeOnshareById() {
        $likeOnShare = $this->buzzDao->getLikeOnShareById(21);
        $this->assertTrue($likeOnShare instanceof LikeOnShare);
        $this->assertEquals(21, $likeOnShare->getId());
    }

    /**
     * this is functoin to test get likeOnShare by id
     */
    public function testGetLikeOnshareByIdWithIncorrectId() {
        $likeOnShare = $this->buzzDao->getLikeOnShareById(210);
        $this->assertTrue($likeOnShare == null);
    }

    /**
     * test shares by employee number
     */
    public function testGetNoOfSharesByEmployeeNumber() {
        $resultShareCount = $this->buzzDao->getNoOfSharesByEmployeeNumber(1);

        $this->assertEquals(1, $resultShareCount);
    }

    /**
     * test shares by employee number
     */
    public function testGetNoOfSharesByEmployeeNumberWithIncorrectId() {
        $resultShareCount = $this->buzzDao->getNoOfSharesByEmployeeNumber(100);

        $this->assertEquals(0, $resultShareCount);
    }

    /**
     * test number of comment by id
     */
    public function testGetNoOfSharesByAdmin() {
        $resultShareCount = $this->buzzDao->getNoOfSharesByEmployeeNumber('');

        $this->assertEquals(1, $resultShareCount);
    }

    /**
     * BuzzDao::getEmployeesHavingAnniversariesNextYear
     */
    public function testGetEmployeesHavingAnniversariesNextYear() {
        $date = '2015-12-15';
        $expectedEmployeeNumber = 4;
        $resultEmployees = $this->buzzDao->getEmployeesHavingAnniversariesNextYear($date);

        $this->assertEquals(1, count($resultEmployees));
        $this->assertEquals($expectedEmployeeNumber, $resultEmployees[0]['emp_number']);
    }

    /**
     * test number of comment  by employee 
     */
    public function testGetNoOfCommentByEmployeeNumber() {
        $resultCommentCount = $this->buzzDao->getNoOfCommentsByEmployeeNumber(1);

        $this->assertEquals(2, $resultCommentCount);
    }

    /**
     * test number of comment  by employee 
     */
    public function testGetNoOfCommentByEmployeeNumberWithIncorrectId() {
        $resultCommentCount = $this->buzzDao->getNoOfCommentsByEmployeeNumber(100);

        $this->assertEquals(0, $resultCommentCount);
    }

    /**
     * test number of comment by Admin
     */
    public function testGetNoOfCommentByAdmin() {
        $resultCommentCount = $this->buzzDao->getNoOfCommentsByEmployeeNumber('');

        $this->assertEquals(1, $resultCommentCount);
    }

    /**
     * test comment by employee number
     */
    public function testGetNoOfCommentFor() {
        $resultCommentCount = $this->buzzDao->getNoOfCommentsForEmployeeByEmployeeNumber(1);

        $this->assertEquals(3, $resultCommentCount);
    }

    /**
     * test number of comment by Admin
     */
    public function testGetNoOfCommentForAdmin() {
        $resultCommentCount = $this->buzzDao->getNoOfCommentsForEmployeeByEmployeeNumber('');

        $this->assertEquals(0, $resultCommentCount);
    }

    /**
     * test number of likes on shares by employee number
     */
    public function testGetNoOfSharesLikeByEmployee() {
        $resultLikeCount = $this->buzzDao->getNoOfShareLikesForEmployeeByEmployeeNumber(1);

        $this->assertEquals(3, $resultLikeCount);
    }

    /**
     * test number of likes on shares by employee number
     */
    public function testGetNoOfSharesLikeByEmployeeWithIncorrectId() {
        $resultLikeCount = $this->buzzDao->getNoOfShareLikesForEmployeeByEmployeeNumber(100);

        $this->assertEquals(0, $resultLikeCount);
    }

    /**
     * test number of comment by id
     */
    public function testGetNoOfSharesLikeByAdmin() {
        $resultLikeCount = $this->buzzDao->getNoOfShareLikesForEmployeeByEmployeeNumber('');

        $this->assertEquals(0, $resultLikeCount);
    }

    /**
     * test comment by employee number
     */
    public function testGetNoOfCommentLikeBy() {
        $resultLikeCount = $this->buzzDao->getNoOfCommentLikesForEmployeeByEmployeeNumber(1);

        $this->assertEquals(3, $resultLikeCount);
    }

    /**
     * test number of comment by Admin
     */
    public function testGetNoOfCommentLikeByAdmin() {
        $resultCommentCount = $this->buzzDao->getNoOfCommentLikesForEmployeeByEmployeeNumber('');

        $this->assertEquals(0, $resultCommentCount);
    }

    /**
     * this is function to test save likes on share to database
     */
    public function testUnLikeOnShare() {
        $unlikeOnShare = new UnLikeOnShare();
        $unlikeOnShare->setShareId(1);
        $unlikeOnShare->setEmployeeNumber(1);
        $unlikeOnShare->setLikeTime('2015-01-10 12:12:12');

        $resultUnlikeOnShare = $this->buzzDao->saveUnLikeForShare($unlikeOnShare);
        $this->assertTrue($resultUnlikeOnShare->getId() != null);
        $this->assertEquals('2015-01-10 12:12:12', $resultUnlikeOnShare->getLikeTime());
    }

    /**
     * this is function to test delete Unlikes on share
     */
    public function testDeleteUnLikeOnshare() {
        $unlikeOnShare = new UnLikeOnShare();
        $unlikeOnShare->setId(20);
        $unlikeOnShare->setShareId(1);
        $unlikeOnShare->setEmployeeNumber(1);
        $unlikeOnShare->setLikeTime('2015-01-10 12:12:12');

        $resultDeleteCount = $this->buzzDao->deleteUnLikeForShare($unlikeOnShare);
        $this->assertEquals(1, $resultDeleteCount);
    }

    /**
     * this is function to test delete likes on share
     */
    public function testDeleteUnLikeOnshareWithIncorrectInput() {
        $unlikeOnShare = new UnLikeOnShare();
        $unlikeOnShare->setId(200);
        $unlikeOnShare->setShareId(10);
        $unlikeOnShare->setEmployeeNumber(10);
        $unlikeOnShare->setLikeTime('2015-01-10 12:12:12');

        $resultDeleteCount = $this->buzzDao->deleteUnLikeForShare($unlikeOnShare);
        $this->assertEquals(0, $resultDeleteCount);
    }

    /**
     * this is function to test delete likes on share
     */
    public function testDeleteUnLikeOnshareByAdmin() {
        $unlikeOnShare = new UnLikeOnShare();
        $unlikeOnShare->setId(20);
        $unlikeOnShare->setShareId(1);
        $unlikeOnShare->setLikeTime('2015-01-10 12:12:12');

        $resultDeleteCount = $this->buzzDao->deleteUnLikeForShare($unlikeOnShare);
        $this->assertEquals(0, $resultDeleteCount);
    }

    /**
     * this is function to test save likes on comment
     */
    public function testUnLikeOnComment() {
        $unlikeOnComment = new UnLikeOnComment();
        $unlikeOnComment->setCommentId(1);
        $unlikeOnComment->setEmployeeNumber(1);
        $unlikeOnComment->setLikeTime('2015-01-10 12:12:12');

        $resultUnlikeOnComment = $this->buzzDao->saveUnLikeForComment($unlikeOnComment);
        $this->assertTrue($resultUnlikeOnComment->getId() != null);
        $this->assertEquals('2015-01-10 12:12:12', $resultUnlikeOnComment->getLikeTime());
    }

    /**
     * this is function to test delete likes on the comment
     */
    public function testDeletUnLikeOnComment() {
        $unlikeOnComment = new UnLikeOnComment();
        $unlikeOnComment->setId(20);
        $unlikeOnComment->setCommentId(1);
        $unlikeOnComment->setEmployeeNumber(1);
        $unlikeOnComment->setLikeTime('2015-01-10 12:12:12');

        $resultDeleteCount = $this->buzzDao->deleteUnLikeForComment($unlikeOnComment);
        $this->assertEquals(1, $resultDeleteCount);
    }

    /**
     * this is function to test delete likes on the comment
     */
    public function testDeletUnLikeOnCommentWithIcorrectValues() {
        $unlikeOnComment = new UnLikeOnComment();
        $unlikeOnComment->setId(20);
        $unlikeOnComment->setCommentId(1);
        $unlikeOnComment->setEmployeeNumber(1);
        $unlikeOnComment->setLikeTime('2015-01-10 12:12:12');

        $resultDeleteCount = $this->buzzDao->deleteUnLikeForComment($unlikeOnComment);
        $this->assertEquals(1, $resultDeleteCount);
    }

    /**
     * this is function to test save likes on comment
     */
    public function testAdminUnLikeOnComment() {
        $unlikeOnComment = new UnLikeOnComment();
        $unlikeOnComment->setCommentId(1);
        $unlikeOnComment->setLikeTime('2015-01-10 12:12:12');

        $resultUnlikeOnComment = $this->buzzDao->saveUnLikeForComment($unlikeOnComment);
        $this->assertTrue($resultUnlikeOnComment->getId() != null);
        $this->assertEquals('2015-01-10 12:12:12', $resultUnlikeOnComment->getLikeTime());
    }

    /**
     * this is function to test delete likes on the comment
     */
    public function testDeletAdminUnLikeOnComment() {
        $unlikeOnComment = new UnLikeOnComment();
        $unlikeOnComment->setCommentId(1);
        $unlikeOnComment->setEmployeeNumber('');

        $resultDeleteCount = $this->buzzDao->deleteUnLikeForComment($unlikeOnComment);
        $this->assertEquals(0, $resultDeleteCount);
    }

    /**
     * test saving photo
     */
    public function testSavingPhoto() {
        $photo = new Photo();
        $photo->setFileType('jpg');
        $photo->setPostId(1);
        $photo->setFilename('test/photo.jpg');
        $resultPhoto = $this->buzzDao->savePhoto($photo);

        $this->assertTrue($resultPhoto->getId() != null);
        $this->assertEquals('jpg', $resultPhoto->getFileType());
    }

    /**
     * test getting anivesary from data base
     */
    public function testEmployeesHavingAnniversaryOnMonthGivingAllResutls() {
        $fromDate = '2015-05-03';
        $resultEmployees = $this->buzzDao->getEmployeesHavingAnniversaryOnMonth($fromDate);

        $this->assertEquals(2, Count($resultEmployees));
    }

    /**
     * test getting anivesary from data base
     */
    public function testEmployeesHavingAnniversaryOnMonthGivingCorrectResutl() {
        $fromDate = '2015-06-03';
        $expectedEmployeeNumber = 2;
        $resultEmployees = $this->buzzDao->getEmployeesHavingAnniversaryOnMonth($fromDate);

        $this->assertEquals(1, Count($resultEmployees));
        $this->assertEquals($expectedEmployeeNumber, $resultEmployees[0]['emp_number']);
    }

    /**
     * test getting anivesary from data base
     */
    public function testEmployeesHavingAnniversaryOnMonthGivingNull() {
        $fromDate = '2015-10-03';
        $resultEmployees = $this->buzzDao->getEmployeesHavingAnniversaryOnMonth($fromDate);

        $this->assertEquals(0, Count($resultEmployees));
    }

    /**
     * test get Most Like shares 
     */
    public function testMostLikeShares() {

        $resultShares = $this->buzzDao->getMostLikedShares(2);

        $this->assertEquals(2, Count($resultShares));
    }

    /**
     * test get Most Like shares 
     */
    public function testMostLikeSharesWhithLageLimit() {

        $resultShares = $this->buzzDao->getMostLikedShares(200);

        $this->assertEquals(2, Count($resultShares));
    }

    /**
     * test get Most Like shares 
     */
    public function testMostLikeSharesGivingCorrectResult() {
        $expectedShareId = 1;
        $resultShares = $this->buzzDao->getMostLikedShares(2);

        $this->assertEquals($expectedShareId, $resultShares[0]['share_id']);
    }

    public function testMostLikeSharesStringLimit() {

        $resultShares = $this->buzzDao->getMostCommentedShares("2");

        $this->assertEquals(2, count($resultShares));
    }

    public function testMostLikeSharesNegativeLimit() {

        $resultShares = $this->buzzDao->getMostCommentedShares(-11);

        $this->assertEquals(2, count($resultShares));
    }

    public function testMostLikeSharesLimitSqlInjection() {
        $pdo = Doctrine_Manager::connection()->getDbh();
        $beforeCount = $pdo->query('SELECT COUNT(*) FROM hs_hr_employee')->fetchColumn();

        $resultShares = $this->buzzDao->getMostCommentedShares("1; delete from hs_hr_employee;");

        $afterCount = $pdo->query('SELECT COUNT(*) FROM hs_hr_employee')->fetchColumn();
        $this->assertEquals($beforeCount, $afterCount);
        $this->assertEquals(2, count($resultShares));
    }

    /**
     * test get most commented shares
     */
    public function testMostCommentedShares() {

        $resultShares = $this->buzzDao->getMostCommentedShares(2);

        $this->assertEquals(2, Count($resultShares));
    }

    /**
     * test get most commented shares with large limit
     */
    public function testMostCommentedSharesWhithLargeLimit() {

        $resultShares = $this->buzzDao->getMostCommentedShares(200);

        $this->assertEquals(2, Count($resultShares));
    }

    /**
     * test get most commented shares result correct
     */
    public function testMostCommentedSharesGivingCorrectResult() {
        $expectedShareId = 1;
        $resultShares = $this->buzzDao->getMostCommentedShares(2);

        $this->assertEquals($expectedShareId, $resultShares[0]['share_id']);
    }

    public function testMostCommentedSharesStringLimit() {

        $resultShares = $this->buzzDao->getMostCommentedShares("2");

        $this->assertEquals(2, count($resultShares));
    }

    public function testMostCommentedSharesNegativeLimit() {

        $resultShares = $this->buzzDao->getMostCommentedShares(-11);

        $this->assertEquals(2, count($resultShares));
    }

    public function testMostCommentedSharesLimitSqlInjection() {
        $pdo = Doctrine_Manager::connection()->getDbh();
        $beforeCount = $pdo->query('SELECT COUNT(*) FROM hs_hr_employee')->fetchColumn();

        $resultShares = $this->buzzDao->getMostCommentedShares("1; delete from hs_hr_employee;");

        $afterCount = $pdo->query('SELECT COUNT(*) FROM hs_hr_employee')->fetchColumn();
        $this->assertEquals($beforeCount, $afterCount);
        $this->assertEquals(2, count($resultShares));
    }

    /**
     * test get more shares
     */
    public function testGetMoreSharesNull() {
        $fromId = 0;
        $limit = 1;
        $resultShares = $this->buzzDao->getMoreShares($limit, $fromId);

        $this->assertEquals(0, Count($resultShares));
    }

    /**
     * test get more shares giving up to limit
     */
    public function testGetMoreSharesGivingLimt() {
        $fromId = 4;
        $limit = 2;
        $resultShares = $this->buzzDao->getMoreShares($limit, $fromId);

        $this->assertTrue($resultShares->getFirst() instanceof Share);
        $this->assertEquals(2, Count($resultShares));
    }

    /**
     * test get more shares giving all when limit large
     */
    public function testGetMoreSharesWithLargeLimit() {
        $fromId = 3;
        $limit = 200;
        $resultShares = $this->buzzDao->getMoreShares($limit, $fromId);

        $this->assertEquals(2, Count($resultShares));
    }

    /**
     * test get more profoile shares Giving null result
     */
    public function testGetMoreEmployeeSharesNullResult() {
        $fromId = 0;
        $limit = 2;
        $employeeNumber = 1;
        $resultShares = $this->buzzDao->getMoreEmployeeSharesByEmployeeNumber($limit, $fromId, $employeeNumber);

        $this->assertEquals(0, Count($resultShares));
    }

    /**
     * test get more profoile shares Giving null result
     */
    public function testGetMoreEmployeeSharesCorrectLimit() {
        $fromId = 5;
        $limit = 2;
        $employeeNumber = 2;
        $resultShares = $this->buzzDao->getMoreEmployeeSharesByEmployeeNumber($limit, $fromId, $employeeNumber);

        $this->assertTrue($resultShares->getFirst() instanceof Share);
        $this->assertEquals(2, Count($resultShares));
    }

    /**
     * test get more profoile shares Giving all when large limit gives
     */
    public function testGetMoreEmployeeSharesWithLargeLimit() {
        $fromId = 5;
        $limit = 200;
        $employeeNumber = 2;
        $resultShares = $this->buzzDao->getMoreEmployeeSharesByEmployeeNumber($limit, $fromId, $employeeNumber);

        $this->assertEquals(2, Count($resultShares));
    }

    /**
     * test get admins shares
     */
    public function testGetMoreSharesByAdmin() {
        $fromId = 5;
        $limit = 2;
        $employeeNumber = '';
        $resultShares = $this->buzzDao->getMoreEmployeeSharesByEmployeeNumber($limit, $fromId, $employeeNumber);

        $this->assertTrue($resultShares->getFirst() instanceof Share);
        $this->assertEquals(1, Count($resultShares));
    }

    public function testGetMoreEmployeeSharesEmpNumberSqlInjection() {
        $fromId = 0;
        $limit = 2;
        $employeeNumber = "5) union select ohrm_user.id,ohrm_user.user_role_id,ohrm_user.emp_number,ohrm_user.user_name,ohrm_user.user_password,ohrm_user.deleted,ohrm_user.purged,ohrm_user.status,ohrm_user.date_entered,ohrm_user.date_modified,ohrm_user.date_modified
from ohrm_user INNER JOIN ohrm_buzz_share where(1=1)";

        // Would generate error if value not properly escaped
        $resultShares = $this->buzzDao->getMoreEmployeeSharesByEmployeeNumber($limit, $fromId, $employeeNumber);

        $this->assertEquals(0, count($resultShares));
    }

    /**
     * test get shares by employee number
     */
    public function testgetSharesByEmployeeNumber() {
        $limit = 1;
        $employeeNumber = 2;
        $resultShares = $this->buzzDao->getSharesByEmployeeNumber($limit, $employeeNumber);

        $this->assertEquals(1, Count($resultShares));
    }

    /**
     * test get shares by admin
     */
    public function testgetSharesByAdmin() {
        $limit = 1;
        $employeeNumber = '';
        $resultShares = $this->buzzDao->getSharesByEmployeeNumber($limit, $employeeNumber);

        $this->assertEquals(1, Count($resultShares));
    }

    /**
     * test get employee shares up to share Id
     */
    public function testGetEmployeeShareUptoId() {
        $lastId = 1;
        $employeeNumber = 2;
        $resultShares = $this->buzzDao->getEmployeeSharesUptoShareId($lastId, $employeeNumber);

        $this->assertEquals(2, Count($resultShares));
    }

    /**
     * test get employee shares up to share Id
     */
    public function testGetEmployeeShareUptoIdWithIncorectId() {
        $lastId = 1;
        $employeeNumber = 20;
        $resultShares = $this->buzzDao->getEmployeeSharesUptoShareId($lastId, $employeeNumber);

        $this->assertEquals(0, Count($resultShares));
    }

    /**
     * test get admin shares upto Id
     */
    public function testGetEmployeeShareUptoIdByAdmin() {
        $lastId = 1;
        $employeeNumber = '';
        $resultShares = $this->buzzDao->getEmployeeSharesUptoShareId($lastId, $employeeNumber);

        $this->assertEquals(1, Count($resultShares));
    }

    /**
     * test get shares up to share Id
     */
    public function testGetShareUpToId() {
        $lastId = 1;
        $resultShares = $this->buzzDao->getSharesUptoId($lastId);


        $this->assertEquals(5, Count($resultShares));
    }

    /**
     * test get shares up to share Id with not existing Id
     */
    public function testGetShareUpToIdWithLageId() {
        $lastId = 100;
        $resultShares = $this->buzzDao->getSharesUptoId($lastId);

        $this->assertEquals(0, Count($resultShares));
    }

    public function testGetPhotoValidId() {
        $photoId = 1;
        $photo = $this->buzzDao->getPhoto($photoId);

        $this->assertInstanceOf('Photo', $photo);
        $this->assertEquals($photoId, $photo->getId());
        $this->assertEquals(1, $photo->getPostId());
        $this->assertEquals('test.jpg', $photo->getFilename());
        $this->assertEquals('jpg', $photo->getFileType());
    }

    public function testGetPhotoNonExistingId() {
        $photoId = 11;
        $photo = $this->buzzDao->getPhoto($photoId);
        $this->assertFalse($photo);
    }

    public function testGetPostPhotosPostWithOnePhoto() {
        $postId = 1;
        $photos = $this->buzzDao->getPostPhotos($postId);

        $this->assertEquals(1, count($photos));
        $this->assertInstanceOf('Photo', $photos[0]);
        $this->assertEquals(1, $photos[0]->getId());
        $this->assertEquals(1, $photos[0]->getPostId());
        $this->assertEquals('test.jpg', $photos[0]->getFilename());
        $this->assertEquals('jpg', $photos[0]->getFileType());
    }

    public function testGetPostPhotosPostWithPhotos() {
        $postId = 4;
        $photos = $this->buzzDao->getPostPhotos($postId);

        $allPhotos = TestDataService::loadObjectList('Photo', $this->fixture, 'Photo');
        $expected = array($allPhotos[2], $allPhotos[3], $allPhotos[4]);

        $this->assertEquals(count($expected), count($photos));
        for ($i = 0; $i < count($expected); $i++) {
            $this->assertEquals($expected[$i]->getId(), $photos[$i]->getId());
            $this->assertEquals($expected[$i]->getPostId(), $photos[$i]->getPostId());
            $this->assertEquals($expected[$i]->getFilename(), $photos[$i]->getFilename());
            $this->assertEquals($expected[$i]->getFileType(), $photos[$i]->getFileType());
        }
    }

    public function testGetPostPhotosPostWithoutPhoto() {
        $postId = 3;
        $photos = $this->buzzDao->getPostPhotos($postId);

        $this->assertEquals(0, count($photos));
    }

    public function testGetSharesFromEmployeeNumber() {
        $employeeNumber = 2;
        $shareCollection = $this->buzzDao->getSharesFromEmployeeNumber($employeeNumber);

        $this->assertEquals(2, count($shareCollection));
        $this->assertEquals(2, $shareCollection[1]['id']);
        $this->assertEquals(3, $shareCollection[1]['post_id']);
        $this->assertEquals('2015-01-01 00:00:00', $shareCollection[1]['share_time']);
        
        $this->assertEquals(3, $shareCollection[0]['id']);
        $this->assertEquals(2, $shareCollection[0]['post_id']);
        $this->assertEquals('2016-01-01 00:00:00', $shareCollection[0]['share_time']);
    }
    
    public function testGetSharesForAdmin() {
        $employeeNumber = 0;
        $shareCollection = $this->buzzDao->getSharesFromEmployeeNumber($employeeNumber);

        $this->assertEquals(1, count($shareCollection));
        $this->assertEquals(4, $shareCollection[0]['id']);
        $this->assertEquals(2, $shareCollection[0]['post_id']);
        $this->assertEquals('admin share', $shareCollection[0]['text']);
    }

}

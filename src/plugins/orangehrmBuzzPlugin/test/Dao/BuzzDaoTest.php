<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Tests\Buzz\Dao;

use DateTime;
use OrangeHRM\Buzz\Dao\BuzzDao;
use OrangeHRM\Buzz\Dto\BuzzCommentSearchFilterParams;
use OrangeHRM\Buzz\Dto\BuzzFeedFilterParams;
use OrangeHRM\Buzz\Dto\BuzzPostShareSearchFilterParams;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\BuzzLink;
use OrangeHRM\Entity\BuzzPhoto;
use OrangeHRM\Entity\BuzzPost;
use OrangeHRM\Entity\BuzzShare;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Buzz
 * @group Dao
 */
class BuzzDaoTest extends KernelTestCase
{
    use EntityManagerHelperTrait;

    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmBuzzPlugin/test/fixtures/BuzzDao.yaml';
        TestDataService::populate($fixture);
    }

    public function testGetBuzzFeedPosts(): void
    {
        $dao = new BuzzDao();
        $buzzFeedFilterParams = new BuzzFeedFilterParams();
        $buzzFeedFilterParams->setAuthUserEmpNumber(1);
        $buzzFeedFilterParams->setLimit(2);
        $buzzFeedFilterParams->setOffset(3);
        $feedPosts = $dao->getBuzzFeedPosts($buzzFeedFilterParams);
        $expected = [
            [
                'employee' => [
                    'empNumber' => 6,
                    'lastName' => 'Morgan',
                    'firstName' => 'Jasmine',
                    'middleName' => '',
                    'employeeId' => '0006',
                    'terminationId' => null,
                ],
                'text' => 'https://youtu.be/qMCMgedYqac',
                'stats' => [
                    'numOfLikes' => 1,
                    'numOfComments' => 1,
                    'numOfShares' => 1,
                ],
                'type' => 'video',
                'shareId' => 5,
                'createdDate' => '2022-10-27',
                'createdTime' => '00:58',
                'originalPost' => null,
                'liked' => false,
                'videoLink' => 'https://www.youtube.com/embed/qMCMgedYqac',
                'photoIds' => null,
                'hasPhotos' => false,
                'hasVideo' => true,
            ],
            [
                'employee' => [
                    'empNumber' => 6,
                    'lastName' => 'Morgan',
                    'firstName' => 'Jasmine',
                    'middleName' => '',
                    'employeeId' => '0006',
                    'terminationId' => null,
                ],
                'text' => 'Write something........',
                'stats' => [
                    'numOfLikes' => 0,
                    'numOfComments' => 0,
                    'numOfShares' => null,
                ],
                'type' => 'text',
                'shareId' => 4,
                'createdDate' => '2022-10-27',
                'createdTime' => '00:54',
                'originalPost' => [
                    'text' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
                    'employee' => [
                        'empNumber' => 1,
                        'lastName' => 'Adalwin',
                        'firstName' => 'Odis',
                        'middleName' => '',
                        'employeeId' => '0001',
                        'terminationId' => null,
                    ],
                    'createdDate' => '2022-10-27',
                    'createdTime' => '00:51',
                ],
                'liked' => false,
                'videoLink' => null,
                'photoIds' => null,
                'hasPhotos' => false,
                'hasVideo' => false,
            ]
        ];
        foreach ($feedPosts as $i => $post) {
            $this->assertEquals($expected[$i]['employee'], $post->getEmployee());
            $this->assertEquals($expected[$i]['text'], $post->getText());
            $this->assertEquals($expected[$i]['stats'], $post->getStats());
            $this->assertEquals($expected[$i]['type'], $post->getType());
            $this->assertEquals($expected[$i]['shareId'], $post->getId());
            $this->assertEquals($expected[$i]['createdDate'], $post->getCreatedDate());
            $this->assertEquals($expected[$i]['createdTime'], $post->getCreatedTime());
            $this->assertEquals($expected[$i]['originalPost'], $post->getOriginalPost());
            $this->assertEquals($expected[$i]['liked'], $post->isLiked());
            $this->assertEquals($expected[$i]['videoLink'], $post->getVideoLink());
            $this->assertEquals($expected[$i]['photoIds'], $post->getPhotoIds());
            $this->assertEquals($expected[$i]['hasPhotos'], $post->hasPhotos());
            $this->assertEquals($expected[$i]['hasVideo'], $post->hasVideo());
        }
    }

    public function testGetBuzzFeedPostsCount(): void
    {
        $dao = new BuzzDao();
        $buzzFeedFilterParams = new BuzzFeedFilterParams();
        $buzzFeedFilterParams->setAuthUserEmpNumber(1);
        $this->assertEquals(8, $dao->getBuzzFeedPostsCount($buzzFeedFilterParams));
        // Out of 10 shares, 3 shares (direct posts) from terminated employees, 2 shares (share of posts) from purged employees
    }

    public function testGetBuzzPhotoIdsByPostId(): void
    {
        $dao = new BuzzDao();
        $this->assertEquals([1, 2, 3, 4], $dao->getBuzzPhotoIdsByPostId(3));

        // Existing post, but don't have photos
        $this->assertEquals([], $dao->getBuzzPhotoIdsByPostId(1));

        // Non-existing post id
        $this->assertEquals([], $dao->getBuzzPhotoIdsByPostId(100));
    }

    public function testSaveBuzzPost(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->method('getNow')
            ->willReturn(new DateTime('2022-11-09 09:10:13'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);

        $buzzPost = new BuzzPost();
        $buzzPost->setText('This is sample text for post 01');
        $buzzPost->getDecorator()->setEmployeeByEmpNumber(1);
        $buzzPost->setCreatedAtUtc();
        $buzzPost->setUpdatedAtUtc();

        $dao = new BuzzDao();
        $result = $dao->saveBuzzPost($buzzPost);
        $this->assertInstanceOf(BuzzPost::class, $result);
        $this->assertInstanceOf(Employee::class, $result->getEmployee());
        $this->assertEquals('This is sample text for post 01', $result->getText());
        $this->assertEquals('2022-11-09', $result->getCreatedAtUtc()->format('Y-m-d'));
        $this->assertEquals('09:10:13', $result->getCreatedAtUtc()->format('H:i:s'));
        $this->assertEquals('2022-11-09', $result->getUpdatedAtUtc()->format('Y-m-d'));
        $this->assertEquals('09:10:13', $result->getUpdatedAtUtc()->format('H:i:s'));
    }

    public function testSaveBuzzShare(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->method('getNow')
            ->willReturn(new DateTime('2022-11-09 11:10:17'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);

        $buzzPost = new BuzzPost();
        $dao = new BuzzDao();
        $buzzPost->setText('This is sample text for post share test');
        $buzzPost->getDecorator()->setEmployeeByEmpNumber(1);
        $buzzPost->setCreatedAtUtc();
        $buzzPost->setUpdatedAtUtc();
        $dao->saveBuzzPost($buzzPost);

        $buzzShare = new BuzzShare();
        $buzzShare->setPost($buzzPost);
        $buzzShare->setText(null);
        $buzzShare->setEmployee($buzzPost->getEmployee());
        $buzzShare->setType(0);
        $buzzShare->setNumOfLikes(1);
        $buzzShare->setNumOfComments(5);
        $buzzShare->setCreatedAtUtc();
        $buzzShare->setUpdatedAtUtc();

        $result = $dao->saveBuzzShare($buzzShare);
        $this->assertInstanceOf(BuzzShare::class, $result);
        $this->assertInstanceOf(BuzzPost::class, $result->getPost());
        $this->assertInstanceOf(Employee::class, $result->getEmployee());
        $this->assertEquals(5, $result->getNumOfComments());
        $this->assertEquals(1, $result->getNumOfLikes());
        $this->assertEquals(0, $result->getType());
        $this->assertEquals('2022-11-09', $result->getCreatedAtUtc()->format('Y-m-d'));
        $this->assertEquals('11:10:17', $result->getCreatedAtUtc()->format('H:i:s'));
        $this->assertEquals('2022-11-09', $result->getUpdatedAtUtc()->format('Y-m-d'));
        $this->assertEquals('11:10:17', $result->getUpdatedAtUtc()->format('H:i:s'));
    }

    public function testSaveBuzzVideo(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->method('getNow')
            ->willReturn(new DateTime('2022-11-09 11:10:17'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);

        $buzzPost = new BuzzPost();
        $buzzPost->setText('This is sample text for video post test');
        $buzzPost->getDecorator()->setEmployeeByEmpNumber(2);
        $buzzPost->setCreatedAtUtc();

        $dao = new BuzzDao();
        $dao->saveBuzzPost($buzzPost);

        $buzzVideo = new BuzzLink();
        $buzzVideo->setPost($buzzPost);
        $buzzVideo->setLink('https://youtu.be/qMCMgedYqac');
        $result = $dao->saveBuzzVideo($buzzVideo);
        $this->assertInstanceOf(BuzzLink::class, $result);
        $this->assertInstanceOf(BuzzPost::class, $result->getPost());
        $this->assertEquals('https://youtu.be/qMCMgedYqac', $result->getLink());
        $this->assertEquals('This is sample text for video post test', $result->getPost()->getText());
    }

    public function testSaveBuzzPhotos(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->method('getNow')
            ->willReturn(new DateTime('2022-11-09 11:10:17'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);

        $buzzPost = new BuzzPost();
        $buzzPost->setText('This is sample text for photo post test');
        $buzzPost->getDecorator()->setEmployeeByEmpNumber(3);
        $buzzPost->setCreatedAtUtc();

        $dao = new BuzzDao();
        $dao->saveBuzzPost($buzzPost);

        $photoPath = Config::get(Config::PLUGINS_DIR) . '/orangehrmBuzzPlugin/test/fixtures/orange.png';
        $buzzPhoto = new BuzzPhoto();
        $buzzPhoto->setPost($buzzPost);
        $buzzPhoto->setPhoto(file_get_contents($photoPath));
        $buzzPhoto->setFilename('orange.png');
        $buzzPhoto->setFileType('image/png');
        $buzzPhoto->setSize(16559);
        $buzzPhoto->setWidth(400);
        $buzzPhoto->setHeight(410);
        $dao->saveBuzzPhoto($buzzPhoto);
        $this->assertFalse(is_resource($buzzPhoto->getPhoto()));

        $photoId = $buzzPhoto->getId();
        $this->getEntityManager()->clear(); // Clear em and refetch entity
        $result = $dao->getBuzzPhotoById($photoId);

        $this->assertInstanceOf(BuzzPhoto::class, $result);
        $this->assertEquals($photoId, $result->getId());
        $this->assertEquals('This is sample text for photo post test', $result->getPost()->getText());
        $this->assertTrue(is_resource($result->getPhoto()));
        $this->assertEquals(file_get_contents($photoPath), $result->getDecorator()->getPhoto());
        // Call twice to check decorator class cache handling
        $this->assertEquals(file_get_contents($photoPath), $result->getDecorator()->getPhoto());
        $this->assertEquals('orange.png', $result->getFilename());
        $this->assertEquals('image/png', $result->getFileType());
        $this->assertEquals('16559', $result->getSize());
        $this->assertEquals('400', $result->getWidth());
        $this->assertEquals('410', $result->getHeight());
    }

    public function testDeleteBuzzPost1(): void
    {
        $dao = new BuzzDao();
        $dao->deleteBuzzPost(1);

        $this->assertEmpty($this->getRepository(BuzzPost::class)->find(1));
        $this->assertEmpty($this->getRepository(BuzzShare::class)->findBy(['post' => 1]));
    }

    public function testDeleteBuzzPost2(): void
    {
        $dao = new BuzzDao();
        $dao->deleteBuzzPost(3);

        $this->assertEmpty($this->getRepository(BuzzPost::class)->find(3));
        $this->assertEmpty($this->getRepository(BuzzShare::class)->findBy(['post' => 3]));
        $this->assertEmpty($this->getRepository(BuzzPhoto::class)->findBy(['post' => 3]));
    }

    public function testDeleteBuzzPost3(): void
    {
        $dao = new BuzzDao();
        $dao->deleteBuzzPost(4);

        $this->assertEmpty($this->getRepository(BuzzPost::class)->find(4));
        $this->assertEmpty($this->getRepository(BuzzShare::class)->findBy(['post' => 4]));
        $this->assertEmpty($this->getRepository(BuzzLink::class)->findBy(['post' => 4]));
    }

    public function testDeleteBuzzShare(): void
    {
        $dao = new BuzzDao();
        $dao->deleteBuzzShare(4);

        $this->assertEmpty($this->getRepository(BuzzShare::class)->find(4));
        $this->assertInstanceOf(BuzzPost::class, $this->getRepository(BuzzPost::class)->find(1));
        $this->assertCount(2, $this->getRepository(BuzzShare::class)->findBy(['post' => 1]));
    }

    public function testDeleteBuzzShare2(): void
    {
        $dao = new BuzzDao();
        $dao->deleteBuzzShare(7);

        $this->assertEmpty($this->getRepository(BuzzShare::class)->find(7));
        $this->assertInstanceOf(BuzzPost::class, $this->getRepository(BuzzPost::class)->find(4));
        $this->assertCount(1, $this->getRepository(BuzzShare::class)->findBy(['post' => 4]));
        $this->assertCount(1, $this->getRepository(BuzzLink::class)->findBy(['post' => 4]));
    }

    public function testGetBuzzShareByPostId(): void
    {
        $dao = new BuzzDao();
        $result = $dao->getBuzzShareByPostId(1);
        $this->assertInstanceOf(BuzzShare::class, $result);
        $this->assertEquals('0', $result->getType());
    }

    public function testGetBuzzPostSharesList1(): void
    {
        $dao = new BuzzDao();
        $buzzPostSharesSearchFilterParams = new BuzzPostShareSearchFilterParams();
        $buzzPostSharesSearchFilterParams->setPostId(1);

        // Get shares of post id 1
        $result = $dao->getBuzzPostSharesList($buzzPostSharesSearchFilterParams);
        $this->assertCount(2, $result);
        $this->assertEquals(4, $result[0]->getId());
        $this->assertEquals(1, $result[0]->getPost()->getId());
        $this->assertEquals(6, $result[0]->getEmployee()->getEmpNumber());
        $this->assertEquals(6, $result[1]->getId());
        $this->assertEquals(1, $result[1]->getPost()->getId());
        $this->assertEquals(5, $result[1]->getEmployee()->getEmpNumber());
    }

    public function testGetBuzzPostSharesList2(): void
    {
        $dao = new BuzzDao();
        $buzzPostSharesSearchFilterParams = new BuzzPostShareSearchFilterParams();
        $buzzPostSharesSearchFilterParams->setPostId(2);

        // Get shares of post id 2 (no reshares)
        $result = $dao->getBuzzPostSharesList($buzzPostSharesSearchFilterParams);
        $this->assertEmpty($result);
    }

    public function testGetBuzzPostSharesCount1(): void
    {
        $dao = new BuzzDao();
        $buzzPostSharesSearchFilterParams = new BuzzPostShareSearchFilterParams();
        $buzzPostSharesSearchFilterParams->setPostId(1);

        // Get shares of post id 1
        $result = $dao->getBuzzPostSharesCount($buzzPostSharesSearchFilterParams);
        $this->assertEquals(2, $result);
    }

    public function testGetBuzzPostSharesCount2(): void
    {
        $dao = new BuzzDao();
        $buzzPostSharesSearchFilterParams = new BuzzPostShareSearchFilterParams();
        $buzzPostSharesSearchFilterParams->setPostId(2);

        // Get shares of post id 2 (no reshares)
        $result = $dao->getBuzzPostSharesCount($buzzPostSharesSearchFilterParams);
        $this->assertEquals(0, $result);
    }

    public function testGetBuzzCommentById(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->method('getNow')
            ->willReturn(new DateTime('2022-12-12 09:10:13'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);

        $dao = new BuzzDao();
        $result = $dao->getBuzzCommentById(1, 2);
        $this->assertEquals('Self Comment', $result->getText());
        $this->assertEquals(2, $result->getNumOfLikes());
    }

    public function testGetBuzzComments(): void
    {
        $dao = new BuzzDao();
        $buzzCommentSearchFilterParams = new BuzzCommentSearchFilterParams();
        $buzzCommentSearchFilterParams->setShareId(2);

        $result = $dao->getBuzzComments($buzzCommentSearchFilterParams);
        $this->assertCount(2, $result);
    }

    public function testGetBuzzCommentsCount(): void
    {
        $dao = new BuzzDao();
        $buzzCommentSearchFilterParams = new BuzzCommentSearchFilterParams();
        $buzzCommentSearchFilterParams->setShareId(1);

        $result = $dao->getBuzzCommentsCount($buzzCommentSearchFilterParams);
        $this->assertEquals(3, $result);
    }
}

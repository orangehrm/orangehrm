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
use OrangeHRM\Buzz\Dao\BuzzLikeDao;
use OrangeHRM\Buzz\Dto\BuzzLikeOnCommentSearchFilterParams;
use OrangeHRM\Buzz\Dto\BuzzLikeOnShareSearchFilterParams;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\BuzzLikeOnComment;
use OrangeHRM\Entity\BuzzLikeOnShare;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Buzz
 * @group Dao
 */
class BuzzLikeDaoTest extends KernelTestCase
{
    use EntityManagerHelperTrait;

    private BuzzLikeDao $buzzLikeDao;

    protected function setUp(): void
    {
        $this->buzzLikeDao = new BuzzLikeDao();

        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNowInUTC'])
            ->getMock();
        $dateTimeHelper->method('getNowInUTC')
            ->willReturn(new DateTime('2022-11-16 14:30:00'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);

        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmBuzzPlugin/test/fixtures/BuzzLikeDao.yaml';
        TestDataService::populate($fixture);
    }

    public function testSaveBuzzLikeOnShare(): void
    {
        $buzzLikeOnShare = new BuzzLikeOnShare();
        $buzzLikeOnShare->getDecorator()->setEmployeeByEmpNumber(1);
        $buzzLikeOnShare->getDecorator()->setShareByShareId(5);
        $buzzLikeOnShare->setLikedAtUtc();

        $buzzLikeOnShare = $this->buzzLikeDao->saveBuzzLikeOnShare($buzzLikeOnShare);

        $this->assertEquals('Devi', $buzzLikeOnShare->getEmployee()->getFirstName());
        $this->assertEquals(5, $buzzLikeOnShare->getShare()->getId());
        $this->assertEquals('2022-11-16', $buzzLikeOnShare->getDecorator()->getLikedAtDate());
        $this->assertEquals('14:30', $buzzLikeOnShare->getDecorator()->getLikedAtTime());
    }

    public function testSaveBuzzLikeOnComment(): void
    {
        $buzzLikeOnComment = new BuzzLikeOnComment();
        $buzzLikeOnComment->getDecorator()->setEmployeeByEmpNumber(1);
        $buzzLikeOnComment->getDecorator()->setCommentByCommentId(5);
        $buzzLikeOnComment->setLikedAtUtc();

        $buzzLikeOnComment = $this->buzzLikeDao->saveBuzzLikeOnComment($buzzLikeOnComment);

        $this->assertEquals('Devi', $buzzLikeOnComment->getEmployee()->getFirstName());
        $this->assertEquals(5, $buzzLikeOnComment->getComment()->getId());
        $this->assertEquals('2022-11-16', $buzzLikeOnComment->getDecorator()->getLikedAtDate());
        $this->assertEquals('14:30', $buzzLikeOnComment->getDecorator()->getLikedAtTime());
    }

    public function testDeleteBuzzLikeOnShare(): void
    {
        $buzzLikeOnShareRepository = $this->getRepository(BuzzLikeOnShare::class);

        $this->buzzLikeDao->deleteBuzzLikeOnShare(1, 1);
        $this->assertEmpty($buzzLikeOnShareRepository->findOneBy(['share' => 1, 'employee' => 1]));
        $this->assertCount(9, $buzzLikeOnShareRepository->findAll());

        $this->buzzLikeDao->deleteBuzzLikeOnShare(3, 4);
        $this->assertEmpty($buzzLikeOnShareRepository->findOneBy(['share' => 3, 'employee' => 4]));
        $this->assertCount(8, $buzzLikeOnShareRepository->findAll());
    }

    public function testDeleteBuzzLikeOnComment(): void
    {
        $buzzLikeOnCommentRepository = $this->getRepository(BuzzLikeOnComment::class);

        $this->buzzLikeDao->deleteBuzzLikeOnComment(5, 1);
        $this->assertEmpty($buzzLikeOnCommentRepository->findOneBy(['comment' => 5, 'employee' => 1]));
        $this->assertCount(3, $buzzLikeOnCommentRepository->findBy(['comment' => 5]));
        $this->assertCount(14, $buzzLikeOnCommentRepository->findAll());

        $this->buzzLikeDao->deleteBuzzLikeOnComment(6, 1);
        $this->assertEmpty($buzzLikeOnCommentRepository->findOneBy(['comment' => 6]));
        $this->assertCount(13, $buzzLikeOnCommentRepository->findAll());
    }

    public function testGetBuzzLikeOnShareByShareIdAndEmpNumber(): void
    {
        $resultLike = $this->buzzLikeDao->getBuzzLikeOnShareByShareIdAndEmpNumber(1, 1);

        $this->assertEquals(1, $resultLike->getId());
        $this->assertEquals('Devi', $resultLike->getEmployee()->getFirstName());
        $this->assertEquals(1, $resultLike->getShare()->getId());
        $this->assertEquals('2022-11-16', $resultLike->getDecorator()->getLikedAtDate());
        $this->assertEquals('05:50', $resultLike->getDecorator()->getLikedAtTime());
    }

    public function testGetBuzzLikeOnCommentByCommentIdAndEmpNumber(): void
    {
        $resultLike = $this->buzzLikeDao->getBuzzLikeOnCommentByShareIdAndEmpNumber(5, 4);

        $this->assertEquals(13, $resultLike->getId());
        $this->assertEquals('Rajitha', $resultLike->getEmployee()->getFirstName());
        $this->assertEquals(5, $resultLike->getComment()->getId());
        $this->assertEquals('2022-11-17', $resultLike->getDecorator()->getLikedAtDate());
        $this->assertEquals('05:17', $resultLike->getDecorator()->getLikedAtTime());
    }

    public function testGetBuzzLikeOnShareList(): void
    {
        $buzzLikeOnShareSearchFilterParams = new BuzzLikeOnShareSearchFilterParams();

        $buzzLikeOnShareSearchFilterParams->setShareId(3);
        $likeList = $this->buzzLikeDao->getBuzzLikeOnShareList($buzzLikeOnShareSearchFilterParams);
        $this->assertCount(3, $likeList);

        $this->assertEquals(7, $likeList[0]->getId());
        $this->assertEquals(3, $likeList[0]->getShare()->getId());
        $this->assertEquals('Rajitha', $likeList[0]->getEmployee()->getFirstName());

        $this->assertEquals(8, $likeList[1]->getId());
        $this->assertEquals(3, $likeList[1]->getShare()->getId());
        $this->assertEquals('Devi', $likeList[1]->getEmployee()->getFirstName());

        $this->assertEquals(9, $likeList[2]->getId());
        $this->assertEquals(3, $likeList[2]->getShare()->getId());
        $this->assertEquals('Sharuka', $likeList[2]->getEmployee()->getFirstName());

        $buzzLikeOnShareSearchFilterParams->setShareId(5);
        $likeList = $this->buzzLikeDao->getBuzzLikeOnShareList($buzzLikeOnShareSearchFilterParams);
        $this->assertEmpty($likeList);
    }

    public function testGetBuzzLikeOnShareCount(): void
    {
        $buzzLikeOnShareSearchFilterParams = new BuzzLikeOnShareSearchFilterParams();

        $buzzLikeOnShareSearchFilterParams->setShareId(1);
        $this->assertEquals(2, $this->buzzLikeDao->getBuzzLikeOnShareCount($buzzLikeOnShareSearchFilterParams));

        $buzzLikeOnShareSearchFilterParams->setShareId(2);
        $this->assertEquals(1, $this->buzzLikeDao->getBuzzLikeOnShareCount($buzzLikeOnShareSearchFilterParams));

        $buzzLikeOnShareSearchFilterParams->setShareId(3);
        $this->assertEquals(3, $this->buzzLikeDao->getBuzzLikeOnShareCount($buzzLikeOnShareSearchFilterParams));

        $buzzLikeOnShareSearchFilterParams->setShareId(4);
        $this->assertEquals(1, $this->buzzLikeDao->getBuzzLikeOnShareCount($buzzLikeOnShareSearchFilterParams));

        $buzzLikeOnShareSearchFilterParams->setShareId(5);
        $this->assertEquals(0, $this->buzzLikeDao->getBuzzLikeOnShareCount($buzzLikeOnShareSearchFilterParams));
    }

    public function testGetBuzzLikeOnCommentList(): void
    {
        $buzzLikeOnCommentFilterParams = new BuzzLikeOnCommentSearchFilterParams();

        $buzzLikeOnCommentFilterParams->setCommentId(5);
        $likeList = $this->buzzLikeDao->getBuzzLikeOnCommentList($buzzLikeOnCommentFilterParams);
        $this->assertCount(3, $likeList);

        $this->assertEquals(11, $likeList[0]->getId());
        $this->assertEquals(5, $likeList[0]->getComment()->getId());
        $this->assertEquals('Sharuka', $likeList[0]->getEmployee()->getFirstName());

        $this->assertEquals(13, $likeList[1]->getId());
        $this->assertEquals(5, $likeList[1]->getComment()->getId());
        $this->assertEquals('Rajitha', $likeList[1]->getEmployee()->getFirstName());

        $this->assertEquals(15, $likeList[2]->getId());
        $this->assertEquals(5, $likeList[2]->getComment()->getId());
        $this->assertEquals('Devi', $likeList[2]->getEmployee()->getFirstName());
    }

    public function testGetBuzzLikeOnCommentCount(): void
    {
        $buzzLikeOnCommentFilterParams = new BuzzLikeOnCommentSearchFilterParams();

        $buzzLikeOnCommentFilterParams->setCommentId(1);
        $this->assertEquals(2, $this->buzzLikeDao->getBuzzLikeOnCommentCount($buzzLikeOnCommentFilterParams));

        $buzzLikeOnCommentFilterParams->setCommentId(2);
        $this->assertEquals(1, $this->buzzLikeDao->getBuzzLikeOnCommentCount($buzzLikeOnCommentFilterParams));

        $buzzLikeOnCommentFilterParams->setCommentId(3);
        $this->assertEquals(3, $this->buzzLikeDao->getBuzzLikeOnCommentCount($buzzLikeOnCommentFilterParams));

        $buzzLikeOnCommentFilterParams->setCommentId(4);
        $this->assertEquals(1, $this->buzzLikeDao->getBuzzLikeOnCommentCount($buzzLikeOnCommentFilterParams));

        $buzzLikeOnCommentFilterParams->setCommentId(5);
        $this->assertEquals(3, $this->buzzLikeDao->getBuzzLikeOnCommentCount($buzzLikeOnCommentFilterParams));

        $buzzLikeOnCommentFilterParams->setCommentId(6);
        $this->assertEquals(1, $this->buzzLikeDao->getBuzzLikeOnCommentCount($buzzLikeOnCommentFilterParams));
    }
}

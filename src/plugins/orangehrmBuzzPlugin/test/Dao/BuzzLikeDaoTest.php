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
 * Boston, MA 02110-1301, USA
 */

namespace OrangeHRM\Tests\Buzz\Dao;

use DateTime;
use OrangeHRM\Buzz\Dao\BuzzLikeDao;
use OrangeHRM\Buzz\Dto\BuzzLikeSearchFilterParams;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
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

    public function testGetBuzzLikeOnShareByShareIdAndEmpNumber(): void
    {
        $resultLike = $this->buzzLikeDao->getBuzzLikeOnShareByShareIdAndEmpNumber(1, 1);

        $this->assertEquals(1, $resultLike->getId());
        $this->assertEquals('Devi', $resultLike->getEmployee()->getFirstName());
        $this->assertEquals(1, $resultLike->getShare()->getId());
        $this->assertEquals('2022-11-16', $resultLike->getDecorator()->getLikedAtDate());
        $this->assertEquals('05:50', $resultLike->getDecorator()->getLikedAtTime());
    }

    public function testGetBuzzLikeOnShareList(): void
    {
        $buzzLikeSearchFilterParams = new BuzzLikeSearchFilterParams();

        $buzzLikeSearchFilterParams->setShareId(3);
        $likeList = $this->buzzLikeDao->getBuzzLikeOnShareList($buzzLikeSearchFilterParams);
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

        $buzzLikeSearchFilterParams->setShareId(5);
        $likeList = $this->buzzLikeDao->getBuzzLikeOnShareList($buzzLikeSearchFilterParams);
        $this->assertEmpty($likeList);
    }

    public function testGetBuzzLikeOnShareCount(): void
    {
        $buzzLikeSearchFilterParams = new BuzzLikeSearchFilterParams();

        $buzzLikeSearchFilterParams->setShareId(1);
        $this->assertEquals(2, $this->buzzLikeDao->getBuzzLikeOnShareCount($buzzLikeSearchFilterParams));

        $buzzLikeSearchFilterParams->setShareId(2);
        $this->assertEquals(1, $this->buzzLikeDao->getBuzzLikeOnShareCount($buzzLikeSearchFilterParams));

        $buzzLikeSearchFilterParams->setShareId(3);
        $this->assertEquals(3, $this->buzzLikeDao->getBuzzLikeOnShareCount($buzzLikeSearchFilterParams));

        $buzzLikeSearchFilterParams->setShareId(4);
        $this->assertEquals(1, $this->buzzLikeDao->getBuzzLikeOnShareCount($buzzLikeSearchFilterParams));

        $buzzLikeSearchFilterParams->setShareId(5);
        $this->assertEquals(0, $this->buzzLikeDao->getBuzzLikeOnShareCount($buzzLikeSearchFilterParams));
    }
}

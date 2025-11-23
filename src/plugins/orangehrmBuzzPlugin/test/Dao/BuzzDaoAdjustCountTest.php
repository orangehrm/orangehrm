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

use OrangeHRM\Buzz\Dao\BuzzDao;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\BuzzComment;
use OrangeHRM\Entity\BuzzShare;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class BuzzDaoAdjustCountTest extends KernelTestCase
{
    use EntityManagerHelperTrait;

    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmBuzzPlugin/test/fixtures/BuzzDaoAdjustCountTest.yaml';
        TestDataService::populate($fixture);
    }

    public function testAdjustLikeAndCommentCountsOnShares(): void
    {
        $dao = new BuzzDao();
        $dao->adjustLikeAndCommentCountsOnShares();

        // Share 02 has 3 liked employees including one purge employee
        $buzzShare = $this->getRepository(BuzzShare::class)->findBy(['post' => 2]);
        $this->assertEquals(2, $buzzShare[0]->getNumOfLikes());

        // Share 11 has 4 likes - post added by purged employee
        $buzzShare = $this->getRepository(BuzzShare::class)->findBy(['post' => 7]);
        $this->assertEquals(0, $buzzShare[0]->getNumOfLikes());

        // Share 01 has 4 comments. One comment added by purged employee
        $buzzShare = $this->getRepository(BuzzShare::class)->findBy(['post' => 1]);
        $this->assertEquals(3, $buzzShare[0]->getNumOfComments());
    }

    public function testAdjustLikeCountOnComments(): void
    {
        $dao = new BuzzDao();
        $dao->adjustLikeCountOnComments();

        // Share 10 has 1 comment. Comment has 2 likes. One added by purged employee
        $buzzComment = $this->getRepository(BuzzComment::class)->findBy(['share' => 10]);
        $this->assertEquals(1, $buzzComment[0]->getNumOfLikes());
    }
}

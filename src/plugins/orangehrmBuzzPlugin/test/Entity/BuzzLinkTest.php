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

namespace OrangeHRM\Tests\Buzz\Entity;

use OrangeHRM\Config\Config;
use OrangeHRM\Entity\BuzzLink;
use OrangeHRM\Entity\BuzzPost;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Buzz
 * @group Entity
 */
class BuzzLinkTest extends EntityTestCase
{
    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmBuzzPlugin/test/fixtures/BuzzLink.yaml';
        TestDataService::populate($fixture);
        TestDataService::truncateSpecificTables([BuzzLink::class]);
    }

    public function testEntity(): void
    {
        $buzzLink = new BuzzLink();
        $buzzLink->setPost($this->getReference(BuzzPost::class, 1));
        $buzzLink->setLink('https://example.com/abcd');
        $this->persist($buzzLink);

        $this->assertEquals(1, $buzzLink->getId());
        $this->assertEquals(1, $buzzLink->getPost()->getId());
        $this->assertEquals('https://example.com/abcd', $buzzLink->getLink());

        $buzzLink->setId(2);
        $this->assertEquals(2, $buzzLink->getId());
    }
}

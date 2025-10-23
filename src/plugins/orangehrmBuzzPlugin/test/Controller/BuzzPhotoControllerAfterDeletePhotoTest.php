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

namespace OrangeHRM\Tests\Buzz\Controller;

use OrangeHRM\Buzz\Controller\File\BuzzPhotoController;
use OrangeHRM\Buzz\Service\BuzzService;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\BuzzPhoto;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\Mock\MockCacheService;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Buzz
 * @group Controller
 */
class BuzzPhotoControllerAfterDeletePhotoTest extends KernelTestCase
{
    public function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmBuzzPlugin/test/fixtures/BuzzPhotoController.yaml';
        TestDataService::populate($fixture);
    }

    public function testHandle(): void
    {
        $cache = MockCacheService::getCache();
        $this->createKernelWithMockServices([
            Services::CACHE => $cache,
            Services::BUZZ_SERVICE => new BuzzService(),
        ]);
        $controller = new BuzzPhotoController();

        // creating cache before delete photo
        $item = $cache->getItem('buzz.photo.1.etag');
        $item->set('"tlLVCXYpPWLcOLV9HQprQMDRYVd8LCnhT4WGf/My+Cc="');
        $cache->save($item);

        // delete buzz photo
        $this->getEntityManager()->remove($this->getEntityReference(BuzzPhoto::class, 1));
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear(); // clear em for new request

        // bad request: photo already deleted, but photo ETag cached
        $request = $this->getHttpRequest([], [], ['id' => 1]);
        $response = $controller->handle($request);
        $this->assertEquals(400, $response->getStatusCode());
    }
}

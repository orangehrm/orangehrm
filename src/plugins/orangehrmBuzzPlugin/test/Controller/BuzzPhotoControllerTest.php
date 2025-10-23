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
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\Mock\MockCacheService;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Buzz
 * @group Controller
 */
class BuzzPhotoControllerTest extends KernelTestCase
{
    public static function setUpBeforeClass(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmBuzzPlugin/test/fixtures/BuzzPhotoController.yaml';
        TestDataService::populate($fixture);
    }

    public function testHandle(): void
    {
        $this->createKernelWithMockServices([
            Services::CACHE => MockCacheService::getCache(),
            Services::BUZZ_SERVICE => new BuzzService(),
        ]);
        $controller = new BuzzPhotoController();
        $request = $this->getHttpRequest([], [], ['id' => '1']);
        $response = $controller->handle($request);
        $this->assertEquals('image/jpeg', $response->headers->get('content-type'));
        $this->assertEquals('max-age=0, must-revalidate, public', $response->headers->get('cache-control'));
        $this->assertEquals('Public', $response->headers->get('pragma'));

        $photoPath = Config::get(Config::PLUGINS_DIR) . '/orangehrmBuzzPlugin/test/fixtures/ohrm_logo.jpg';
        $this->assertEquals('"tlLVCXYpPWLcOLV9HQprQMDRYVd8LCnhT4WGf/My+Cc="', $response->getEtag());
        $this->assertEquals(file_get_contents($photoPath), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());

        $request = $this->getHttpRequest(
            [],
            [],
            ['id' => 1],
            [],
            [],
            ['HTTP_IF_NONE_MATCH' => '"tlLVCXYpPWLcOLV9HQprQMDRYVd8LCnhT4WGf/My+Cc="']
        );
        $response = $controller->handle($request);
        $this->assertEquals('"tlLVCXYpPWLcOLV9HQprQMDRYVd8LCnhT4WGf/My+Cc="', $response->getEtag());
        $this->assertEquals(304, $response->getStatusCode());
        $this->assertNull($response->headers->get('content-type'));
    }

    public function testHandleMultipleRequestWithoutETag(): void
    {
        $this->createKernelWithMockServices([
            Services::CACHE => MockCacheService::getCache(),
            Services::BUZZ_SERVICE => new BuzzService(),
        ]);
        $controller = new BuzzPhotoController();
        $request = $this->getHttpRequest([], [], ['id' => 2]);
        $response = $controller->handle($request);
        $this->assertEquals('image/png', $response->headers->get('content-type'));
        $this->assertEquals('max-age=0, must-revalidate, public', $response->headers->get('cache-control'));
        $this->assertEquals('Public', $response->headers->get('pragma'));

        $photoPath = Config::get(Config::PLUGINS_DIR) . '/orangehrmBuzzPlugin/test/fixtures/orange.png';
        $this->assertEquals('"9zzsx8kLGgloEq9Z08golAVI3LRotSQqqBGVijvzwzI="', $response->getEtag());
        $this->assertEquals(file_get_contents($photoPath), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());

        $request = $this->getHttpRequest([], [], ['id' => 2]);
        $response = $controller->handle($request);
        $this->assertEquals('"9zzsx8kLGgloEq9Z08golAVI3LRotSQqqBGVijvzwzI="', $response->getEtag());
        $this->assertEquals(file_get_contents($photoPath), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testHandleNonExistingPhotoId(): void
    {
        $this->createKernelWithMockServices([
            Services::CACHE => MockCacheService::getCache(),
            Services::BUZZ_SERVICE => new BuzzService(),
        ]);
        $controller = new BuzzPhotoController();
        $request = $this->getHttpRequest([], [], ['id' => 1000]);
        $response = $controller->handle($request);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testHandleWithoutId(): void
    {
        $controller = new BuzzPhotoController();
        $request = $this->getHttpRequest();
        $response = $controller->handle($request);
        $this->assertEquals(400, $response->getStatusCode());
    }
}

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

namespace OrangeHRM\Tests\CorporateBranding\Controller;

use OrangeHRM\Config\Config;
use OrangeHRM\CorporateBranding\Controller\File\ImageController;
use OrangeHRM\CorporateBranding\Service\ThemeService;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\Mock\MockCacheService;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group CorporateBranding
 * @group Controller
 */
class ImageControllerTest extends KernelTestCase
{
    public static function setUpBeforeClass(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmCorporateBrandingPlugin/test/fixtures/Theme.yaml';
        TestDataService::populate($fixture);
    }

    /**
     * @dataProvider dataProviderForController
     */
    public function testHandle(string $imageName, int $size, string $etag): void
    {
        $this->createKernelWithMockServices([
            Services::CACHE => MockCacheService::getCache(),
            Services::THEME_SERVICE => new ThemeService(),
        ]);
        $controller = new ImageController();
        $request = $this->getHttpRequest([], [], ['imageName' => $imageName]);
        $response = $controller->handle($request);
        $this->assertEquals('image/png', $response->headers->get('content-type'));
        $this->assertEquals('max-age=0, must-revalidate, public', $response->headers->get('cache-control'));
        $this->assertEquals('Public', $response->headers->get('pragma'));
        $this->assertEquals($etag, $response->getEtag());
        $this->assertEquals(
            $size,
            mb_strlen(hex2bin(substr($response->getContent(), 2, strlen($response->getContent()))), '8bit')
        );
        $this->assertEquals(200, $response->getStatusCode());

        $request = $this->getHttpRequest(
            [],
            [],
            ['imageName' => $imageName],
            [],
            [],
            ['HTTP_IF_NONE_MATCH' => $etag]
        );
        $response = $controller->handle($request);
        $this->assertEquals($etag, $response->getEtag());
        $this->assertEquals(304, $response->getStatusCode());
        $this->assertNull($response->headers->get('content-type'));
    }

    public function dataProviderForController(): array
    {
        return [
            ['clientLogo', 2431, '"H6ghn8AXo59TTi/JWzTXXlxwljZidr52nmtd5nGsUvI="'],
            ['clientBanner', 2680, '"F/F1KzfrFokwFz5+AQ4Yn+x+v9D84LRsOc75RW81yYk="'],
            ['loginBanner', 21848, '"pUT3e5s40yHdWjdEQm1bNjVN+sfjRyfYdVDzJzC3jH4="'],
        ];
    }
}

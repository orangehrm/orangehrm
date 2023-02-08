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

namespace OrangeHRM\Tests\CorporateBranding\Service;

use OrangeHRM\Config\Config;
use OrangeHRM\CorporateBranding\Service\ThemeService;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\CorporateBranding\Api\ThemeAPITest;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\Mock\MockCacheService;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group CorporateBranding
 * @group Service
 */
class ThemeServiceTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmCorporateBrandingPlugin/test/fixtures/ThemeService.yaml';
        TestDataService::populate($fixture);
    }

    public function testShowSocialMediaImages(): void
    {
        $this->createKernelWithMockServices([
            Services::CACHE => MockCacheService::getCache(),
        ]);
        $themeService = new ThemeService();
        $this->assertFalse($themeService->showSocialMediaImages()); // check custom theme
        $this->assertFalse($themeService->showSocialMediaImages()); // call twice to check caching

        ThemeAPITest::keepOnlyDefaultThemePreHook();
        $this->createKernelWithMockServices([
            Services::CACHE => MockCacheService::getCache(),
        ]);

        $this->assertTrue($themeService->showSocialMediaImages()); // check default theme
        $this->assertTrue($themeService->showSocialMediaImages()); // call twice to check caching
    }

    public function testGetCurrentThemeVariables(): void
    {
        $this->createKernelWithMockServices([
            Services::CACHE => MockCacheService::getCache(),
        ]);
        $customThemeCssVariables = [
            '--oxd-primary-one-color' => '#FD7B1D',
            '--oxd-primary-font-color' => '#FFFFFD',
            '--oxd-secondary-four-color' => '#76AC21',
            '--oxd-secondary-font-color' => '#FFFFFD',
            '--oxd-primary-gradient-start-color' => '#FD920B',
            '--oxd-primary-gradient-end-color' => '#D35C17',
            '--oxd-secondary-gradient-start-color' => '#FD920B',
            '--oxd-secondary-gradient-end-color' => '#D35C17',
            '--oxd-primary-one-lighten-5-color' => '#fd8a36',
            '--oxd-primary-one-lighten-30-color' => '#fed4b5',
            '--oxd-primary-one-darken-5-color' => '#fd6c04',
            '--oxd-primary-one-alpha-10-color' => 'rgba(253, 123, 29, 0.1)',
            '--oxd-primary-one-alpha-15-color' => 'rgba(253, 123, 29, 0.15)',
            '--oxd-primary-one-alpha-20-color' => 'rgba(253, 123, 29, 0.2)',
            '--oxd-primary-one-alpha-50-color' => 'rgba(253, 123, 29, 0.5)',
            '--oxd-secondary-four-lighten-5-color' => '#85c125',
            '--oxd-secondary-four-darken-5-color' => '#67971d',
            '--oxd-secondary-four-alpha-10-color' => 'rgba(118, 172, 33, 0.1)',
            '--oxd-secondary-four-alpha-15-color' => 'rgba(118, 172, 33, 0.15)',
            '--oxd-secondary-four-alpha-20-color' => 'rgba(118, 172, 33, 0.2)',
            '--oxd-secondary-four-alpha-50-color' => 'rgba(118, 172, 33, 0.5)',
        ];
        $themeService = new ThemeService();
        $this->assertEquals($customThemeCssVariables, $themeService->getCurrentThemeVariables()); // check custom theme
        $this->assertEquals(
            $customThemeCssVariables,
            $themeService->getCurrentThemeVariables()
        ); // call twice to check caching

        ThemeAPITest::keepOnlyDefaultThemePreHook();
        $this->createKernelWithMockServices([
            Services::CACHE => MockCacheService::getCache(),
        ]);

        $customThemeCssVariables = [
            '--oxd-primary-one-color' => '#FF7B1D',
            '--oxd-primary-font-color' => '#FFFFFF',
            '--oxd-secondary-four-color' => '#76BC21',
            '--oxd-secondary-font-color' => '#FFFFFF',
            '--oxd-primary-gradient-start-color' => '#FF920B',
            '--oxd-primary-gradient-end-color' => '#F35C17',
            '--oxd-secondary-gradient-start-color' => '#FF920B',
            '--oxd-secondary-gradient-end-color' => '#F35C17',
            '--oxd-primary-one-lighten-5-color' => '#ff8a37',
            '--oxd-primary-one-lighten-30-color' => '#ffd4b6',
            '--oxd-primary-one-darken-5-color' => '#ff6c03',
            '--oxd-primary-one-alpha-10-color' => 'rgba(255, 123, 29, 0.1)',
            '--oxd-primary-one-alpha-15-color' => 'rgba(255, 123, 29, 0.15)',
            '--oxd-primary-one-alpha-20-color' => 'rgba(255, 123, 29, 0.2)',
            '--oxd-primary-one-alpha-50-color' => 'rgba(255, 123, 29, 0.5)',
            '--oxd-secondary-four-lighten-5-color' => '#84d225',
            '--oxd-secondary-four-darken-5-color' => '#68a61d',
            '--oxd-secondary-four-alpha-10-color' => 'rgba(118, 188, 33, 0.1)',
            '--oxd-secondary-four-alpha-15-color' => 'rgba(118, 188, 33, 0.15)',
            '--oxd-secondary-four-alpha-20-color' => 'rgba(118, 188, 33, 0.2)',
            '--oxd-secondary-four-alpha-50-color' => 'rgba(118, 188, 33, 0.5)',
        ];
        $this->assertEquals($customThemeCssVariables, $themeService->getCurrentThemeVariables()); // check default theme
        $this->assertEquals(
            $customThemeCssVariables,
            $themeService->getCurrentThemeVariables()
        ); // call twice to check caching
    }

    public function testGetImageETagWithNullImage(): void
    {
        ThemeAPITest::keepOnlyDefaultThemePreHook();
        $this->createKernelWithMockServices([
            Services::CACHE => MockCacheService::getCache(),
        ]);
        $themeService = new ThemeService();
        $this->assertNull($themeService->getImageETag('client_logo'));
    }

    public function testGetImage(): void
    {
        $this->createKernelWithMockServices([
            Services::CACHE => MockCacheService::getCache(),
        ]);
        $themeService = new ThemeService();
        $image = $themeService->getImage('client_logo');
        $this->assertEquals('ohrm_logo.png', $image->getFilename());
    }

    public function testGetImageWithNullImage(): void
    {
        ThemeAPITest::keepOnlyDefaultThemePreHook();
        $this->createKernelWithMockServices([
            Services::CACHE => MockCacheService::getCache(),
        ]);
        $themeService = new ThemeService();
        $this->assertNull($themeService->getImage('client_logo'));
    }
}

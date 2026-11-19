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

namespace OrangeHRM\Tests\Core\Installer;

use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Installer\Util\AppSetupUtility;
use OrangeHRM\Installer\Util\ConfigHelper;
use OrangeHRM\Tests\Util\TestCase;

/**
 * Base URL recorded by the installer/upgrader for links in emails.
 *
 * @group Core
 * @group Installer
 */
class AppSetupUtilityInstanceBaseUrlTest extends TestCase
{
    public function testSaveInstanceBaseUrlStoresWhenUnset(): void
    {
        $configHelper = $this->getMockBuilder(ConfigHelper::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getConfigValue', 'setConfigValue'])
            ->getMock();
        $configHelper->method('getConfigValue')
            ->with(ConfigService::KEY_INSTANCE_BASE_URL)
            ->willReturn(null);
        $configHelper->expects($this->once())
            ->method('setConfigValue')
            ->with(ConfigService::KEY_INSTANCE_BASE_URL, 'https://hr.example.com/orangehrm/web/index.php');

        $this->assertTrue(
            $this->getAppSetupUtility($configHelper)
                ->saveInstanceBaseUrl('https://hr.example.com/orangehrm/web/index.php/')
        );
    }

    public function testSaveInstanceBaseUrlKeepsExistingValue(): void
    {
        $configHelper = $this->getMockBuilder(ConfigHelper::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getConfigValue', 'setConfigValue'])
            ->getMock();
        $configHelper->method('getConfigValue')
            ->willReturn('https://hr.example.com/orangehrm/web/index.php');
        $configHelper->expects($this->never())->method('setConfigValue');

        $this->assertFalse(
            $this->getAppSetupUtility($configHelper)->saveInstanceBaseUrl('http://staging.example.com/web/index.php')
        );
    }

    public function testSaveInstanceBaseUrlReplacesStoredValueWhenEnteredByAdmin(): void
    {
        $configHelper = $this->getMockBuilder(ConfigHelper::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getConfigValue', 'setConfigValue'])
            ->getMock();
        $configHelper->method('getConfigValue')
            ->willReturn('http://staging.example.com/web/index.php');
        $configHelper->expects($this->once())
            ->method('setConfigValue')
            ->with(ConfigService::KEY_INSTANCE_BASE_URL, 'https://hr.example.com/orangehrm/web/index.php');

        $this->assertTrue(
            $this->getAppSetupUtility($configHelper)
                ->saveInstanceBaseUrl('https://hr.example.com/orangehrm/web/index.php', true)
        );
    }

    public function testGetStoredInstanceBaseUrl(): void
    {
        $configHelper = $this->getMockBuilder(ConfigHelper::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getConfigValue'])
            ->getMock();
        $configHelper->method('getConfigValue')
            ->with(ConfigService::KEY_INSTANCE_BASE_URL)
            ->willReturnOnConsecutiveCalls('HTTPS://HR.example.com/web/index.php/', 'not a url', null);
        $appSetupUtility = $this->getAppSetupUtility($configHelper);

        $this->assertSame('https://hr.example.com/web/index.php', $appSetupUtility->getStoredInstanceBaseUrl());
        $this->assertNull($appSetupUtility->getStoredInstanceBaseUrl());
        $this->assertNull($appSetupUtility->getStoredInstanceBaseUrl());
    }

    public function testSaveInstanceBaseUrlReplacesInvalidStoredValue(): void
    {
        $configHelper = $this->getMockBuilder(ConfigHelper::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getConfigValue', 'setConfigValue'])
            ->getMock();
        $configHelper->method('getConfigValue')->willReturn('not a url');
        $configHelper->expects($this->once())
            ->method('setConfigValue')
            ->with(ConfigService::KEY_INSTANCE_BASE_URL, 'http://hr.example.com/web/index.php');

        $this->assertTrue(
            $this->getAppSetupUtility($configHelper)->saveInstanceBaseUrl('http://hr.example.com/web/index.php')
        );
    }

    /**
     * @dataProvider invalidBaseUrlDataProvider
     */
    public function testSaveInstanceBaseUrlIgnoresMissingOrInvalidValue(?string $baseUrl): void
    {
        $configHelper = $this->getMockBuilder(ConfigHelper::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getConfigValue', 'setConfigValue'])
            ->getMock();
        $configHelper->expects($this->never())->method('getConfigValue');
        $configHelper->expects($this->never())->method('setConfigValue');

        $this->assertFalse($this->getAppSetupUtility($configHelper)->saveInstanceBaseUrl($baseUrl));
    }

    public function invalidBaseUrlDataProvider(): array
    {
        return [[null], [''], ['hr.example.com'], ['ftp://hr.example.com/'], ['https://hr.example.com/?a=b']];
    }

    private function getAppSetupUtility(ConfigHelper $configHelper): AppSetupUtility
    {
        return new class ($configHelper) extends AppSetupUtility {
            private ConfigHelper $mockConfigHelper;

            public function __construct(ConfigHelper $configHelper)
            {
                $this->mockConfigHelper = $configHelper;
            }

            protected function getConfigHelper(): ConfigHelper
            {
                return $this->mockConfigHelper;
            }
        };
    }
}

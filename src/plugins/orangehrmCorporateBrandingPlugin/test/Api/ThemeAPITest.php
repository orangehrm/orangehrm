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

namespace OrangeHRM\Tests\CorporateBranding\Api;

use OrangeHRM\CorporateBranding\Api\ThemeAPI;
use OrangeHRM\CorporateBranding\Service\ThemeService;
use OrangeHRM\Entity\Theme;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;

/**
 * @group CorporateBranding
 * @group APIv2
 */
class ThemeAPITest extends EndpointIntegrationTestCase
{
    /**
     * @dataProvider dataProviderForTestGetOne
     */
    public function testGetOne(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('ThemeAPI.yaml');

        $this->registerServices($testCaseParams);
        $api = $this->getApiEndpointMock(ThemeAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getOne', $testCaseParams);
    }

    /**
     * @return array
     */
    public function dataProviderForTestGetOne(): array
    {
        return $this->getTestCases('ThemeAPITestCases.yaml', 'GetOne');
    }

    public static function keepOnlyDefaultThemePreHook(): void
    {
        $theme = Doctrine::getEntityManager()->getRepository(Theme::class)->findOneBy(
            ['name' => ThemeService::CUSTOM_THEME]
        );
        Doctrine::getEntityManager()->remove($theme);
        Doctrine::getEntityManager()->flush();
    }

    /**
     * @dataProvider dataProviderForTestDelete
     */
    public function testDelete(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('ThemeAPI.yaml');

        $this->registerServices($testCaseParams);
        $api = $this->getApiEndpointMock(ThemeAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'delete', $testCaseParams);
    }

    /**
     * @return array
     */
    public function dataProviderForTestDelete(): array
    {
        return $this->getTestCases('ThemeAPITestCases.yaml', 'Delete');
    }

    /**
     * @dataProvider dataProviderForTestUpdate
     */
    public function testUpdate(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('ThemeAPI.yaml');
        $this->registerServices($testCaseParams);
        $api = $this->getApiEndpointMock(ThemeAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'update', $testCaseParams);
    }

    /**
     * @return array
     */
    public function dataProviderForTestUpdate(): array
    {
        return $this->getTestCases('ThemeAPITestCases.yaml', 'Update');
    }
}

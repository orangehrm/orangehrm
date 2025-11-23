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

namespace OrangeHRM\Tests\CorporateBranding\Dto;

use OrangeHRM\Config\Config;
use OrangeHRM\CorporateBranding\Dto\PartialTheme;
use OrangeHRM\CorporateBranding\Service\ThemeService;
use OrangeHRM\Entity\Theme;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group CorporateBranding
 * @group Dto
 */
class PartialThemeTest extends TestCase
{
    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmCorporateBrandingPlugin/test/fixtures/Theme.yaml';
        TestDataService::populate($fixture);
    }

    public function testCreateFromTheme(): void
    {
        $theme = Doctrine::getEntityManager()->getRepository(Theme::class)->findOneBy(
            ['name' => ThemeService::CUSTOM_THEME]
        );
        $partialTheme = PartialTheme::createFromTheme($theme);
        $this->assertEquals(2, $partialTheme->getId());
        $this->assertEquals('custom', $partialTheme->getName());
    }
}

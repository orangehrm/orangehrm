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

namespace OrangeHRM\Tests\CorporateBranding\Dao;

use OrangeHRM\Config\Config;
use OrangeHRM\CorporateBranding\Dao\ThemeDao;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group CorporateBranding
 * @group Dao
 */
class ThemeDaoTest extends KernelTestCase
{
    private ThemeDao $themeDao;
    private string $fixture;

    protected function setUp(): void
    {
        $this->themeDao = new ThemeDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmCorporateBrandingPlugin/test/fixtures/ThemeDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetImageByImageKeyAndThemeName(): void
    {
        $content = $this->themeDao->getImageByImageKeyAndThemeName('client_logo')->getContent();
        $this->assertEquals(4864, mb_strlen($content));
        $content = $this->themeDao->getImageByImageKeyAndThemeName('client_banner')->getContent();
        $this->assertEquals(17148, mb_strlen($content));
        $content = $this->themeDao->getImageByImageKeyAndThemeName('login_banner')->getContent();
        $this->assertEquals(44836, mb_strlen($content));

        $content = $this->themeDao->getImageByImageKeyAndThemeName('login_banner', 'default');
        $this->assertNull($content);

        $content = $this->themeDao->getImageByImageKeyAndThemeName('login_banner', 'not-exists');
        $this->assertNull($content);
    }
}

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

namespace OrangeHRM\Tests\CorporateBranding\Entity;

use OrangeHRM\Entity\Theme;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group CorporateBranding
 * @group Entity
 */
class ThemeTest extends EntityTestCase
{
    public function testEntity(): void
    {
        TestDataService::truncateSpecificTables([Theme::class]);
        $theme = new Theme();
        $theme->setId(1);
        $theme->setName('orange');
        $theme->setVariables(['primary' => '#FD7B1D']);
        $theme->setClientLogo('##logo##');
        $theme->setClientLogoFilename('ohrm_logo.png');
        $theme->setClientLogoFileType('image/png');
        $theme->setClientLogoFileSize(100);
        $theme->setClientBanner('##banner##');
        $theme->setClientBannerFilename('orangehrm_banner.jpg');
        $theme->setClientBannerFileType('image/jpg');
        $theme->setClientBannerFileSize(200);
        $theme->setLoginBanner('##login##');
        $theme->setLoginBannerFilename('ohrm_login.jpeg');
        $theme->setLoginBannerFileType('image/jpeg');
        $theme->setLoginBannerFileSize(300);
        $theme->setShowSocialMediaIcons(false);

        $this->assertEquals(1, $theme->getId());
        $this->assertEquals('orange', $theme->getName());
        $this->assertEquals(['primary' => '#FD7B1D'], $theme->getVariables());
        $this->assertEquals('##logo##', $theme->getClientLogo());
        $this->assertEquals('ohrm_logo.png', $theme->getClientLogoFilename());
        $this->assertEquals('image/png', $theme->getClientLogoFileType());
        $this->assertEquals(100, $theme->getClientLogoFileSize());
        $this->assertEquals('##banner##', $theme->getClientBanner());
        $this->assertEquals('orangehrm_banner.jpg', $theme->getClientBannerFilename());
        $this->assertEquals('image/jpg', $theme->getClientBannerFileType());
        $this->assertEquals(200, $theme->getClientBannerFileSize());
        $this->assertEquals('##login##', $theme->getLoginBanner());
        $this->assertEquals('ohrm_login.jpeg', $theme->getLoginBannerFilename());
        $this->assertEquals('image/jpeg', $theme->getLoginBannerFileType());
        $this->assertEquals(300, $theme->getLoginBannerFileSize());
        $this->assertFalse($theme->showSocialMediaIcons());
    }
}

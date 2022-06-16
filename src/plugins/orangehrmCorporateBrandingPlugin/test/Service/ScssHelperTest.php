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

use OrangeHRM\CorporateBranding\Service\ScssHelper;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group CorporateBranding
 * @group Service
 */
class ScssHelperTest extends TestCase
{
    public function testDarken(): void
    {
        $helper = new ScssHelper();
        $this->assertEquals('#68a61d', $helper->darken('#76bc21', '5%'));
        $this->assertEquals('#d2080e', $helper->darken('#eb0910', '5%'));
        $this->assertEquals('#580306', $helper->darken('#eb0910', '30%'));
    }

    public function testLighten(): void
    {
        $helper = new ScssHelper();
        $this->assertEquals('#ff8a37', $helper->lighten('#ff7b1d', '5%'));
        $this->assertEquals('#ffd4b6', $helper->lighten('#ff7b1d', '30%'));
    }

    public function testRgba(): void
    {
        $helper = new ScssHelper();
        $this->assertEquals('rgba(255, 123, 29, 0.1)', $helper->rgba('#ff7b1d', 0.1));
        $this->assertEquals('rgba(255, 123, 29, 0.15)', $helper->rgba('#ff7b1d', 0.15));
        $this->assertEquals('rgba(255, 123, 29, 0.2)', $helper->rgba('#ff7b1d', 0.2));
        $this->assertEquals('rgba(255, 123, 29, 0.5)', $helper->rgba('#ff7b1d', 0.5));
    }
}

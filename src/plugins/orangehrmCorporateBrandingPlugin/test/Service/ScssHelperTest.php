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
        $this->assertEquals('#68a61d', $helper->darken('#76bc21', 5));
        $this->assertEquals('black', $helper->darken('#76bc21', '100%'));
        $this->assertEquals('#76bc21', $helper->darken('#76bc21', '0%'));
        $this->assertEquals('#d2080e', $helper->darken('#eb0910', '5%'));
        $this->assertEquals('#580306', $helper->darken('#eb0910', '30%'));
        $this->assertEquals('#ea0000', $helper->darken('rgba(255, 30, 30, 1)', '10%'));
        $this->assertEquals('rgba(234, 0, 0, 0.5)', $helper->darken('rgba(255, 30, 30, 0.5)', '10%'));
        $this->assertEquals('#ff0509', $helper->darken('hsla(359, 100%, 61%, 1)', '10%'));
        $this->assertEquals('rgba(255, 5, 9, 0.5)', $helper->darken('hsla(359, 100%, 61%, 0.5)', '10%'));
    }

    public function testLighten(): void
    {
        $helper = new ScssHelper();
        $this->assertEquals('#ff8a37', $helper->lighten('#ff7b1d', '5%'));
        $this->assertEquals('#ff8a37', $helper->lighten('#ff7b1d', 5));
        $this->assertEquals('#ffd4b6', $helper->lighten('#ff7b1d', '30%'));
        $this->assertEquals('white', $helper->lighten('#ff7b1d', '100%'));
    }

    public function testRgba(): void
    {
        $helper = new ScssHelper();
        $this->assertEquals('rgba(255, 123, 29, 0.1)', $helper->rgba('#ff7b1d', 0.1));
        $this->assertEquals('rgba(255, 123, 29, 0.15)', $helper->rgba('#ff7b1d', 0.15));
        $this->assertEquals('rgba(255, 123, 29, 0.2)', $helper->rgba('#ff7b1d', 0.2));
        $this->assertEquals('rgba(255, 123, 29, 0.5)', $helper->rgba('#ff7b1d', 0.5));
        $this->assertEquals('#ff7b1d', $helper->rgba('#ff7b1d', 1));
    }

    public function testIsColor(): void
    {
        $helper = new ScssHelper();
        $this->assertTrue($helper->isValidColor('#ff7b1d'));
        $this->assertTrue($helper->isValidColor('rgba(255, 123, 29, 0.15)'));
        $this->assertTrue($helper->isValidColor('rgba(#ff7b1d, 0.15)'));
        $this->assertTrue($helper->isValidColor('rgb(#ff7b1d)'));
        $this->assertTrue($helper->isValidColor('hsla(359, 100%, 61%, 0.5)'));
        $this->assertTrue($helper->isValidColor('hsla(359, 100%, 61%, .5)'));
        $this->assertTrue($helper->isValidColor('#ff7b1d12'));
        $this->assertTrue($helper->isValidColor('#fff'));
        $this->assertTrue($helper->isValidColor('white'));
        $this->assertFalse($helper->isValidColor(''));
        $this->assertFalse($helper->isValidColor('*'));
        $this->assertFalse($helper->isValidColor('fff'));
        $this->assertFalse($helper->isValidColor('123'));
        $this->assertFalse($helper->isValidColor('#'));
    }
}

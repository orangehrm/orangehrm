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
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Tests\Core\Service;

use OrangeHRM\Core\Service\TextHelperService;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Core
 * @group Service
 */
class TextHelperServiceTest extends TestCase
{
    private TextHelperService $textHelperService;

    protected function setUp(): void
    {
        $this->textHelperService = new TextHelperService();
    }

    public function testStrLength(): void
    {
        $this->assertEquals(4, $this->textHelperService->strLength('1234'));
        $this->assertEquals(4, $this->textHelperService->strLength('1234'), '8bit');

        $this->assertEquals(8, $this->textHelperService->strLength('පරීක්ෂණය'));
        $this->assertEquals(24, $this->textHelperService->strLength('පරීක්ෂණය', '8bit'));
    }

    public function testStrContains(): void
    {
        $this->assertTrue($this->textHelperService->strContains('1234', '1'));
        $this->assertFalse($this->textHelperService->strContains('1234', '5'));
        $this->assertTrue($this->textHelperService->strContains('1234', ''));
        $this->assertTrue($this->textHelperService->strContains('පරීක්ෂණය', 'ර'));
        $this->assertTrue($this->textHelperService->strContains('පරීක්ෂණය', 'රී'));
    }

    public function testStrStartsWith(): void
    {
        $this->assertTrue($this->textHelperService->strStartsWith('1234', '1'));
        $this->assertFalse($this->textHelperService->strStartsWith('1234', '2'));
        $this->assertTrue($this->textHelperService->strStartsWith('Test', ''));
        $this->assertTrue($this->textHelperService->strStartsWith('පරීක්ෂණය', 'ප'));
        $this->assertTrue($this->textHelperService->strStartsWith('රිය', 'රි'));
        $this->assertTrue($this->textHelperService->strStartsWith('රිය', 'ර'));
    }

    public function testStrEndsWith(): void
    {
        $this->assertTrue($this->textHelperService->strEndsWith('1234', '4'));
        $this->assertTrue($this->textHelperService->strEndsWith('1234', ''));
        $this->assertFalse($this->textHelperService->strEndsWith('1234', '1'));
        $this->assertTrue($this->textHelperService->strEndsWith('නරියා', 'යා'));
        $this->assertTrue($this->textHelperService->strEndsWith('නරියා', 'ා'));
    }
}

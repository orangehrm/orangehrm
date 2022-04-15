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

namespace OrangeHRM\Tests\Maintenance\Dto;

use OrangeHRM\Maintenance\Dto\InfoArray;
use OrangeHRM\Tests\Util\TestCase;

class InfoArrayTest extends TestCase
{
    private InfoArray $infoArray1;
    private InfoArray $infoArray2;

    protected function setUp(): void
    {
        $strategyInfoArray = [
            'match_by' => [
                ['match' => 'empNumber']
            ],
            'parameters' => [
                ['field' => 'firstName', 'class' => 'FormatWithPurgeString'],
                ['field' => 'middleName', 'class' => 'FormatWithEmptyString'],
                ['field' => 'smoker', 'class' => 'FormatWithZero'],
                ['field' => 'birthday', 'class' => 'FormatWithNull'],
                ['field' => 'purgedAt', 'class' => 'FormatWithPurgeTime']
            ]
        ];
        $this->infoArray1 = new InfoArray($strategyInfoArray);

        $strategyInfoArray2 = [
            'match_by' => [
                ['match' => 'employee']
            ]
        ];
        $this->infoArray2 = new InfoArray($strategyInfoArray2);
    }

    public function testGetMatchArray(): void
    {
        $expected1 = ['match' => 'empNumber'];
        $expected2 = ['match' => 'employee'];

        $this->assertEquals($expected1, $this->infoArray1->getMatchArray());
        $this->assertEquals($expected2, $this->infoArray2->getMatchArray());
    }

    public function testGetParameterArray(): void
    {
        $expected = [
            ['field' => 'firstName', 'class' => 'FormatWithPurgeString'],
            ['field' => 'middleName', 'class' => 'FormatWithEmptyString'],
            ['field' => 'smoker', 'class' => 'FormatWithZero'],
            ['field' => 'birthday', 'class' => 'FormatWithNull'],
            ['field' => 'purgedAt', 'class' => 'FormatWithPurgeTime']
        ];
        $this->assertEquals($expected, $this->infoArray1->getParameterArray());

        $this->assertNull($this->infoArray2->getParameterArray());
    }
}

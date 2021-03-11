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

/**
 * @group ohrmWidget
 */
class ohrmReportWidgetJoinedDateTest extends PHPUnit\Framework\TestCase
{
    /**
     * @var ohrmReportWidgetJoinedDate
     */
    private $ohrmReportWidgetJoinedDate = null;

    protected function setUp(): void
    {
        $this->ohrmReportWidgetJoinedDate = $this->getMockBuilder(ohrmReportWidgetJoinedDate::class)
            ->setMethods(['configure'])
            ->getMock();
    }

    /**
     * @dataProvider generateWhereClausePartDataProvider
     */
    public function testGenerateWhereClausePart($value, $expected)
    {
        $this->ohrmReportWidgetJoinedDate->setWhereClauseCondition($value['comparision']);
        $returnValue = $this->ohrmReportWidgetJoinedDate->generateWhereClausePart('hs_hr_employee.joined_date', $value);
        $this->assertEquals($expected, $returnValue);
    }

    /**
     * @return Generator
     */
    public function generateWhereClausePartDataProvider()
    {
        // >
        yield [
            [
                'comparision' => '1',
                'value1' => '2021-03-10',
                'value2' => ''
            ],
            "hs_hr_employee.joined_date > '2021-03-10'"
        ];

        // <
        yield [
            [
                'comparision' => '2',
                'value1' => '2021-03-10',
                'value2' => ''
            ],
            "hs_hr_employee.joined_date < '2021-03-10'"
        ];

        // BETWEEN
        yield [
            [
                'comparision' => '3',
                'value1' => '2021-03-10',
                'value2' => '2021-03-20'
            ],
            "( hs_hr_employee.joined_date BETWEEN '2021-03-10' AND '2021-03-20' )"
        ];
    }

    /**
     * @dataProvider generateWhereClausePartWithSqlDataProvider
     * @param $value
     * @param $expected
     */
    public function testGenerateWhereClausePartWithSql($value, $expected)
    {
        $this->ohrmReportWidgetJoinedDate->setWhereClauseCondition($value['comparision']);
        $returnValue = $this->ohrmReportWidgetJoinedDate->generateWhereClausePart('hs_hr_employee.joined_date', $value);
        $this->assertEquals($expected, $returnValue);
    }

    /**
     * @return Generator
     */
    public function generateWhereClausePartWithSqlDataProvider()
    {
        // >
        yield [
            [
                'comparision' => '1',
                'value1' => '2021-03-10;DELETE FROM `hs_hr_employee` WHERE `hs_hr_employee`.`emp_number` = "1";',
                'value2' => ''
            ],
            'hs_hr_employee.joined_date > \'2021-03-10;DELETE FROM `hs_hr_employee` WHERE `hs_hr_employee`.`emp_number` = \"1\";\'',
        ];

        // <
        yield [
            [
                'comparision' => '2',
                'value1' => '2021-03-10;DELETE FROM `hs_hr_employee` WHERE `hs_hr_employee`.`emp_number` = "1";',
                'value2' => ''
            ],
            'hs_hr_employee.joined_date < \'2021-03-10;DELETE FROM `hs_hr_employee` WHERE `hs_hr_employee`.`emp_number` = \"1\";\'',
        ];

        // BETWEEN
        yield [
            [
                'comparision' => '3',
                'value1' => '2021-03-10;DELETE FROM `hs_hr_employee` WHERE `hs_hr_employee`.`emp_number` = "1";',
                'value2' => '2021-03-20'
            ],
            '( hs_hr_employee.joined_date BETWEEN \'2021-03-10;DELETE FROM `hs_hr_employee` WHERE `hs_hr_employee`.`emp_number` = \"1\";\' AND \'2021-03-20\' )'
        ];
    }
}

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
class ohrmWidgetDateRangeTest  extends PHPUnit\Framework\TestCase
{
    /**
     * @var ohrmWidgetDateRange
     */
    private $ohrmWidgetDateRange = null;

    protected function setUp(): void
    {
        $this->ohrmWidgetDateRange = $this->getMockBuilder(ohrmWidgetDateRange::class)
            ->setMethods(['configure', 'getTodayDate', 'getDatePickerDateFormat'])
            ->getMock();
    }

    public function testGenerateWhereClausePartWithValidDates()
    {
        $returnValue = $this->ohrmWidgetDateRange->generateWhereClausePart('date',
            ['from' => '2021-02-01', 'to' => '2021-03-31']);
        $this->assertEquals("( date BETWEEN '2021-02-01' AND '2021-03-31' )",$returnValue);
    }

    public function testGenerateWhereClausePartWithValidFromDate()
    {
        $this->ohrmWidgetDateRange->expects($this->once())
            ->method('getTodayDate')
            ->will($this->returnValue('2021-03-01'));
        $this->ohrmWidgetDateRange->expects($this->once())
            ->method('getDatePickerDateFormat')
            ->will($this->returnValue('yy-mm-dd'));
        $returnValue = $this->ohrmWidgetDateRange->generateWhereClausePart('date',
            ['from' => '2021-02-01', 'to' => 'yy-mm-dd']);
        $this->assertEquals("( date BETWEEN '2021-02-01' AND '2021-03-01' )",$returnValue);
    }

    public function testGenerateWhereClausePartWithValidToDate()
    {
        $this->ohrmWidgetDateRange->expects($this->once())
            ->method('getTodayDate')
            ->will($this->returnValue('2021-03-01'));
        $this->ohrmWidgetDateRange->expects($this->once())
            ->method('getDatePickerDateFormat')
            ->will($this->returnValue('yy-mm-dd'));
        $returnValue = $this->ohrmWidgetDateRange->generateWhereClausePart('date',
            ['from' => 'yy-mm-dd', 'to' => '2021-03-25']);
        $this->assertEquals("( date BETWEEN '1970-01-01' AND '2021-03-25' )",$returnValue);
    }

    public function testGenerateWhereClausePartWithNoDates()
    {
        $this->ohrmWidgetDateRange->expects($this->once())
            ->method('getTodayDate')
            ->will($this->returnValue('2021-03-01'));
        $this->ohrmWidgetDateRange->expects($this->once())
            ->method('getDatePickerDateFormat')
            ->will($this->returnValue('yy-mm-dd'));
        $returnValue = $this->ohrmWidgetDateRange->generateWhereClausePart('date',
            ['from' => '', 'to' => '']);
        $this->assertEquals("( date BETWEEN '1970-01-01' AND '2021-03-01' )",$returnValue);
    }

    public function testGenerateWhereClausePartWithSql()
    {
        $returnValue = $this->ohrmWidgetDateRange->generateWhereClausePart(
            'date',
            [
                'from' => '2021-02-01;DELETE FROM `hs_hr_employee` WHERE `hs_hr_employee`.`emp_number` = 1;',
                'to' => '2021-03-31;DELETE FROM `hs_hr_employee` WHERE `hs_hr_employee`.`emp_number` = 1;']);
        $this->assertEquals(
            "( date BETWEEN '2021-02-01;DELETE FROM `hs_hr_employee` WHERE `hs_hr_employee`.`emp_number` = 1;' " .
            "AND '2021-03-31;DELETE FROM `hs_hr_employee` WHERE `hs_hr_employee`.`emp_number` = 1;' )",$returnValue);
    }
}
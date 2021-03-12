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
class ohrmWidgetDateIntervalTest  extends PHPUnit\Framework\TestCase
{
    /**
     * @var ohrmWidgetDateInterval
     */
    private $ohrmWidgetDateInterval = null;

    protected function setUp(): void
    {
        $this->ohrmWidgetDateInterval = $this->getMockBuilder(ohrmWidgetDateInterval::class)
            ->setMethods(['configure', 'getTodayDate'])
            ->getMock();
    }

    public function testGenerateWhereClausePartWithValidDates()
    {
        $returnValue = $this->ohrmWidgetDateInterval->generateWhereClausePart('date',
            ['from' => '2021-02-01', 'to' => '2021-03-31']);
        $this->assertEquals(" (  ( ( date BETWEEN '2021-02-01' AND '2021-03-31' )" .
            " AND (  BETWEEN '2021-02-01' AND '2021-03-31' ) )  " .
            "OR  (  ( date > '2021-02-01' AND date < '2021-03-31' )  AND  (  > ''2021-03-31'' )  )  " .
            "OR  (  ( date < '2021-02-01' )  AND  (  > '2021-02-01' AND  < '2021-03-31' )  )  ) ",$returnValue);
    }

    public function testGenerateWhereClausePartWithValidFromDate()
    {
        $this->ohrmWidgetDateInterval->expects($this->once())
            ->method('getTodayDate')
            ->will($this->returnValue('2021-03-01'));
        $returnValue = $this->ohrmWidgetDateInterval->generateWhereClausePart('date',
            ['from' => '2021-02-01', 'to' => 'YYYY-MM-DD']);
        $this->assertEquals(" (  ( ( date BETWEEN '2021-02-01' AND '2021-03-01' ) " .
            "AND (  BETWEEN '2021-02-01' AND '2021-03-01' ) )  OR  (  ( date > '2021-02-01' AND date < '2021-03-01' ) " .
            " AND  (  > ''2021-03-01'' )  )  OR  (  ( date < '2021-02-01' )  " .
            "AND  (  > '2021-02-01' AND  < '2021-03-01' )  )  ) ",$returnValue);
    }

    public function testGenerateWhereClausePartWithValidToDate()
    {
        $this->ohrmWidgetDateInterval->expects($this->once())
            ->method('getTodayDate')
            ->will($this->returnValue('2021-03-01'));
        $returnValue = $this->ohrmWidgetDateInterval->generateWhereClausePart('date',
            ['from' => 'YYYY-MM-DD', 'to' => '2021-03-25']);
        $this->assertEquals(" (  ( ( date BETWEEN '1970-01-01' AND '2021-03-25' )" .
            " AND (  BETWEEN '1970-01-01' AND '2021-03-25' ) ) " .
            " OR  (  ( date > '1970-01-01' AND date < '2021-03-25' )  AND  (  > ''2021-03-25'' )  )  " .
            "OR  (  ( date < '1970-01-01' )  AND  (  > '1970-01-01' AND  < '2021-03-25' )  )  ) ",$returnValue);
    }

    public function testGenerateWhereClausePartWithSql()
    {
        $returnValue = $this->ohrmWidgetDateInterval->generateWhereClausePart(
            'date',
            [
                'from' => '2021-02-01;DELETE FROM `hs_hr_employee` WHERE `hs_hr_employee`.`emp_number` = 1;',
                'to' => '2021-03-31;DELETE FROM `hs_hr_employee` WHERE `hs_hr_employee`.`emp_number` = 1;']);
        $this->assertEquals(
            " (  ( ( date BETWEEN '2021-02-01;DELETE FROM `hs_hr_employee` WHERE `hs_hr_employee`.`emp_number` = 1;'" .
            " AND '2021-03-31;DELETE FROM `hs_hr_employee` WHERE `hs_hr_employee`.`emp_number` = 1;' )" .
            " AND (  BETWEEN '2021-02-01;DELETE FROM `hs_hr_employee` WHERE `hs_hr_employee`.`emp_number` = 1;'" .
            " AND '2021-03-31;DELETE FROM `hs_hr_employee` WHERE `hs_hr_employee`.`emp_number` = 1;' ) ) " .
            " OR  (  ( date > '2021-02-01;DELETE FROM `hs_hr_employee` WHERE `hs_hr_employee`.`emp_number` = 1;'" .
            " AND date < '2021-03-31;DELETE FROM `hs_hr_employee` WHERE `hs_hr_employee`.`emp_number` = 1;' )" .
            "  AND  (  > ''2021-03-31;DELETE FROM `hs_hr_employee` WHERE `hs_hr_employee`.`emp_number` = 1;'' )  ) " .
            " OR  (  ( date < '2021-02-01;DELETE FROM `hs_hr_employee` WHERE `hs_hr_employee`.`emp_number` = 1;' ) " .
            " AND  (  > '2021-02-01;DELETE FROM `hs_hr_employee` WHERE `hs_hr_employee`.`emp_number` = 1;'" .
            " AND  < '2021-03-31;DELETE FROM `hs_hr_employee` WHERE `hs_hr_employee`.`emp_number` = 1;' )  )  ) ",
            $returnValue);
    }
}
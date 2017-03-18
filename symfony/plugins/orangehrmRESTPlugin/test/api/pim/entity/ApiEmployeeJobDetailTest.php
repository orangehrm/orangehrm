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
 * Test class of Api/EmployeeService
 *
 * @group API
 */
use Orangehrm\Rest\Api\Pim\Entity\EmployeeJobDetail;

class ApiEmployeeJobDetailTest extends PHPUnit_Framework_TestCase
{

    /**
     * Set up method
     */
    protected function setUp()
    {

    }

    public function testToArray(){


        $testJobDetailsArray = array(

            'title' => 'Engineer',
            'category' => 'Engineering',
            'joinedDate' => '2017-10-30',
            'startDate' => '2017-11-30',
            'endDate' => '2018-11-30',
            'status'  => 'Active',
            'subunit'=>  'Engineering',
            'location'=> 'Eng Dept'
        );

        $employeeJobDetail = new EmployeeJobDetail();

        $employeeJobDetail->setEndDate('2018-11-30');
        $employeeJobDetail->setStartDate('2017-11-30');
        $employeeJobDetail->setCategory('Engineering');
        $employeeJobDetail->setJoinedDate('2017-10-30');
        $employeeJobDetail->setTitle('Engineer');
        $employeeJobDetail->setEmploymentStatus('Active');
        $employeeJobDetail->setSubunit('Engineering');
        $employeeJobDetail->setLocation('Eng Dept');


        $this->assertEquals($testJobDetailsArray, $employeeJobDetail->toArray());

    }

}

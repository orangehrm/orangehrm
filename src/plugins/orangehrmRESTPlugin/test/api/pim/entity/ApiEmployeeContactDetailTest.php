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
use Orangehrm\Rest\Api\Pim\Entity\EmployeeContactDetail;


class ApiEmployeeContactDetailTest extends PHPUnit_Framework_TestCase
{

    /**
     * Set up method
     */
    protected function setUp()
    {

    }

    public function testToArray(){


        $testContactsArray = array(

            'id' => 1,
            'code' => '001',
            'fullName' => 'Nina Jane Lewis',
            'addressStreet1' => 'River street vancour',
            'addressStreet2' => 'No 45 Park road',
            'city' => 'Vancour',
            'state' => 'Western',
            'zip' => '4433',
            'county' => 'Canada',
            'homeTelephone' => '081612323',
            'workTelephone' => '0123123123',
            'mobile' => '345345345',
            'workEmail' => 'nina@orange.com',
            'otherEmail' => 'nina@yahoo.com'
        );

        $employeeContactDetail = new EmployeeContactDetail("Nina Jane Lewis", '001');

        $employeeContactDetail->setWorkTelephone('0123123123');
        $employeeContactDetail->setWorkEmail('nina@orange.com');
        $employeeContactDetail->setAddressStreet1('River street vancour');
        $employeeContactDetail->setAddressStreet2('No 45 Park road');
        $employeeContactDetail->setCity('Vancour');
        $employeeContactDetail->setState('Western');
        $employeeContactDetail->setZip('4433');
        $employeeContactDetail->setCountry('Canada');
        $employeeContactDetail->setHomeTelephone('081612323');
        $employeeContactDetail->setMobile('345345345');
        $employeeContactDetail->setOtherEmail('nina@yahoo.com');
        $employeeContactDetail->setId(1);

        $this->assertEquals($testContactsArray, $employeeContactDetail->toArray());

    }

}
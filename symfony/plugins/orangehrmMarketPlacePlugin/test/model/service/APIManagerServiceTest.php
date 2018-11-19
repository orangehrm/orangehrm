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

/**
 * Class APIManagerServiceTest
 */
class APIManagerServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function setUp()
    {

    }

    /**
     *
     */
    public function testGetAddons()
    {
        $output = array(
            [
                "id" => 1,
                "name" => "addon1",
                "summary" => "summery for addon 1",
                "description" => "dec for addon 1",
                "icon" => null,
                "status" => "enabled",
                "date" => "2018-12-01T00:00:00+13:00"
            ],
            [
                "id" => 2,
                "name" => "addon2",
                "summary" => "summery for addon 2",
                "description" => "dec for addon 2",
                "icon" => null,
                "status" => "enabled",
                "date" => "2018-11-01T00:00:00+13:00"
            ],
            [
                "id" => 3,
                "name" => "addon3",
                "summary" => "summery for addon 3",
                "description" => "dec for addon 3",
                "icon" => null,
                "status" => "enabled",
                "date" => "2018-10-01T00:00:00+13:00"
            ]
        );
        $apiManagerService = $this->getMockBuilder('APIManagerService')
            ->setMethods(array('getAddonsFromMP'))
            ->getMock();
        $apiManagerService->expects($this->once())
            ->method('getAddonsFromMP')
            ->will($this->returnValue($output));
        $result = $apiManagerService->getAddons();
        $this->assertEquals(gettype($result), 'array');
        $this->assertEquals(sizeof($result[0]), 7);
        $this->assertArrayHasKey('id', $result[0]);
        $this->assertArrayHasKey('name', $result[0]);
        $this->assertArrayHasKey('summary', $result[0]);
        $this->assertArrayHasKey('description', $result[0]);
        $this->assertArrayHasKey('icon', $result[0]);
        $this->assertArrayHasKey('status', $result[0]);
        $this->assertArrayHasKey('date', $result[0]);
        $this->assertEquals($result[0]['id'], 1);
        $this->assertEquals($result[1]['name'], 'addon2');
        $this->assertEquals($result[2]['summary'], 'summery for addon 3');
        $this->assertEquals($result[2]['description'], 'dec for addon 3');
    }

    /**
     * 
     */
    public function testGetDescription()
    {
        $output = array(
            "id" => 1,
            "name" => "addon1",
            "description" => "long des for addon1"
        );
        $apiManagerService = $this->getMockBuilder('APIManagerService')
            ->setMethods(array('getDescriptionFromMP'))
            ->getMock();
        $apiManagerService->expects($this->once())
            ->method('getDescriptionFromMP')
            ->will($this->returnValue($output));
        $result = $apiManagerService->getDescription(1);
        $this->assertEquals(gettype($result), 'array');
        $this->assertEquals(sizeof($result), 3);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertEquals($result['id'], 1);
        $this->assertEquals($result['name'], 'addon1');
        $this->assertEquals($result['description'], 'long des for addon1');
    }
}

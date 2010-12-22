<?php
/*
 *
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
 *
*/


class WorkWeekServiceTest extends PHPUnit_Framework_TestCase
{

    private $workWeekService;
    private $fixture;

    protected function setUp()
    {
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmCoreLeavePlugin/test/fixtures/WorkWeekService.yml';
        $this->workWeekService	=	new WorkWeekService();
    }

    /* test setWorkWeekDao works well */
    
    public function testSetGetWorkWeekDao() {

       $workWeekDao = new WorkWeekDao();
       $this->workWeekService->setWorkWeekDao($workWeekDao);

       $this->assertTrue($this->workWeekService->getWorkWeekDao() instanceof WorkWeekDao);
       $this->assertEquals($workWeekDao, $this->workWeekService->getWorkWeekDao());

    }

    /* test for saveWorkWeek */

    public function testSaveWorkWeek() {

      $workWeekList   = TestDataService::loadObjectList('WorkWeek', $this->fixture, 'WorkWeek');
      $workWeek       = $workWeekList[0];

      $workWeekDao = $this->getMock('WorkWeekDao', array('saveWorkWeek'));
      $workWeekDao->expects($this->once())
                  ->method('saveWorkWeek')
                  ->with($workWeek)
                  ->will($this->returnValue($workWeek));

      $this->workWeekService->setWorkWeekDao($workWeekDao);
      
      $this->assertTrue($this->workWeekService->saveWorkWeek($workWeek) instanceof WorkWeek);

    }

    /* test for getWorkWeekList */
    
    public function testGetWorkWeekList() {

      $workWeekList   = TestDataService::loadObjectList('WorkWeek', $this->fixture, 'WorkWeek');
      
      $workWeekDao = $this->getMock('WorkWeekDao', array('getWorkWeekList'));
      $workWeekDao->expects($this->once())
                  ->method('getWorkWeekList')
                  ->will($this->returnValue($workWeekList));

      $this->workWeekService->setWorkWeekDao($workWeekDao);
      $list = $this->workWeekService->getWorkWeekList();

      $this->assertEquals(7, count($list));
      foreach ($list as $workWeek) {
         $this->assertTrue($workWeek instanceof WorkWeek);
      }

    }

    /* test readWorkWeek returns WorkWeek instance */

    public function testReadWorkWeek() {

      $workWeekList   = TestDataService::loadObjectList('WorkWeek', $this->fixture, 'WorkWeek');

      $workWeekDao = $this->getMock('WorkWeekDao', array('readWorkWeek'));
      $workWeekDao->expects($this->once())
                  ->method('readWorkWeek')
                  ->with(1)
                  ->will($this->returnValue($workWeekList[0]));

      $this->workWeekService->setWorkWeekDao($workWeekDao);
      $readWorkWeek = $this->workWeekService->readWorkWeek(1);

      $this->assertTrue($readWorkWeek instanceof WorkWeek);
      $this->assertEquals($workWeekList[0], $readWorkWeek);

    }

    /* test readWorkWeek returns null in Dao */

    public function testReadWorkWeekReturnsNullInDao() {

      $workWeekDao = $this->getMock('WorkWeekDao', array('readWorkWeek'));
      $workWeekDao->expects($this->once())
                  ->method('readWorkWeek')
                  ->with(8)
                  ->will($this->returnValue(null));

      $this->workWeekService->setWorkWeekDao($workWeekDao);
      $readWorkWeek = $this->workWeekService->readWorkWeek(8);
      
      $this->assertTrue($readWorkWeek instanceof WorkWeek);

    }

    /* test isWeekend */
    
    public function testIsWeekend() {

      $workWeekDao = $this->getMock('WorkWeekDao', array('isWeekend'));
      $workWeekDao->expects($this->once())
                  ->method('isWeekend')
                  ->with(1, true)
                  ->will($this->returnValue(true));

      $this->workWeekService->setWorkWeekDao($workWeekDao);
      $this->assertTrue($this->workWeekService->isWeekend(1, true));

    }

    /* test deleteWorkWeek */
    
    public function testDeleteWorkWeek() {

      $workWeekDao = $this->getMock('WorkWeekDao', array('deleteWorkWeek'));
      $workWeekDao->expects($this->once())
                  ->method('deleteWorkWeek')
                  ->with(array(1,2))
                  ->will($this->returnValue(true));

      $this->workWeekService->setWorkWeekDao($workWeekDao);
      $this->assertTrue($this->workWeekService->deleteWorkWeek(array(1, 2)));

    }

}

?>
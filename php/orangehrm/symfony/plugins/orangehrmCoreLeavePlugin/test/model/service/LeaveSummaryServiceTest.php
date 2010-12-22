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

class LeaveSummaryServiceTest extends PHPUnit_Framework_TestCase {

    protected $leaveSummaryService;

    public function setup() {

        $this->leaveSummaryService  = new LeaveSummaryService();

    }

    public function testFetchRawLeaveSummaryRecords() {

        $clues = array();
        $offset = 0;
        $limit = 50;
        $resource = 'mysql_resource';

        $leaveSummaryDao = $this->getMock('LeaveSummaryDao', array('fetchRawLeaveSummaryRecords'));
        $leaveSummaryDao->expects($this->once())
                        ->method('fetchRawLeaveSummaryRecords')
                        ->with($clues, $offset, $limit)
                        ->will($this->returnValue($resource));

        $this->leaveSummaryService->setLeaveSummaryDao($leaveSummaryDao);

        $result = $this->leaveSummaryService->fetchRawLeaveSummaryRecords($clues, $offset, $limit);

        $this->assertEquals($resource, $result);

    }

    public function testFetchRawLeaveSummaryRecordsCount() {

        $clues = array();

        $leaveSummaryDao = $this->getMock('LeaveSummaryDao', array('fetchRawLeaveSummaryRecordsCount'));
        $leaveSummaryDao->expects($this->once())
                        ->method('fetchRawLeaveSummaryRecordsCount')
                        ->with($clues)
                        ->will($this->returnValue(50));

        $this->leaveSummaryService->setLeaveSummaryDao($leaveSummaryDao);

        $result = $this->leaveSummaryService->fetchRawLeaveSummaryRecordsCount($clues);

        $this->assertEquals(50, $result);

    }

}

?>

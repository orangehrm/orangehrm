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
 * Description of BuzzTimezoneUtilityTest
 *
 * @author amila
 * @group buzz
 */
class BuzzTimezoneUtilityTest extends PHPUnit\Framework\TestCase {

    private $buzzTimezoneUtility;

    /**
     * Set up method
     */
    protected function setUp(): void {
        $this->buzzTimezoneUtility = new BuzzTimezoneUtility();
        $this->defualtUser = sfContext::getInstance()->getUser();
        $this->defualtServerTimeZone = date_default_timezone_get();
    }

    public function tearDown(): void{
        sfContext::getInstance()->set('user',$this->defualtUser);
        date_default_timezone_set($this->defualtServerTimeZone);
    }

    public function testGetTimeZoneFromClientOffset() {
        $content = array(
            '5.5'=>'+5:30',
            '2.25'=>'+2:15',
            '-5.5'=>'-5:30',
            '+5.75'=>'+5:45',
            );
        foreach ($content as $offset => $expected){
         $this->assertEquals($expected, $this->buzzTimezoneUtility->gettimeZoneFromClientOffset($offset));
        }

        $content = array(
            '5.5'=>'5:30',
        );
        foreach ($content as $offset => $expected){
            $this->assertNotEquals($expected, $this->buzzTimezoneUtility->gettimeZoneFromClientOffset($offset));
        }
    }

    public function testGetShareTime(){
        date_default_timezone_set('America/Los_Angeles');
        sfContext::getInstance()->getUser()->setAttribute('system.timeZoneOffset', 1);
        $share = new Share();
        $share->setShareTime('2018-03-01 11:17:18');
        $shareTime = $share->getShareTime();
        $this->assertEquals('2018-03-01 20:17:18', $shareTime, 'share time for client timezone is incorrect');
    }

    public function testGetShareTimeForGMT(){
        date_default_timezone_set('America/Los_Angeles');
        sfContext::getInstance()->getUser()->setAttribute('system.timeZoneOffset', 0);
        $share = new Share();
        $share->setShareTime('2018-03-01 11:17:18');
        $shareTime = $share->getShareTime();
        $this->assertEquals('2018-03-01 19:17:18', $shareTime, 'share time for client timezone is incorrect');
    }

    public function testGetShareTimeForMinusOffset(){
        date_default_timezone_set('America/Los_Angeles');
        sfContext::getInstance()->getUser()->setAttribute('system.timeZoneOffset', -4);
        $share = new Share();
        $share->setShareTime('2018-03-01 11:17:18');
        $shareTime = $share->getShareTime();
        $this->assertEquals('2018-03-01 15:17:18', $shareTime, 'share time for client timezone is incorrect');
    }

    public function testGetCommentTime(){
        date_default_timezone_set('America/Los_Angeles');
        sfContext::getInstance()->getUser()->setAttribute('system.timeZoneOffset', 1);
        $comment = new Comment();
        $comment->setCommentTime('2018-03-01 11:17:18');
        $commentTime = $comment->getCommentTime();
        $this->assertEquals('2018-03-01 20:17:18', $commentTime, 'comment time for client timezone is incorrect');
    }

    public function testGetCommentTimeForGMT(){
        date_default_timezone_set('America/Los_Angeles');
        sfContext::getInstance()->getUser()->setAttribute('system.timeZoneOffset', 0);
        $comment = new Comment();
        $comment->setCommentTime('2018-03-01 11:17:18');
        $commentTime = $comment->getCommentTime();
        $this->assertEquals('2018-03-01 19:17:18', $commentTime, 'comment time for client timezone is incorrect');
    }

    public function testGetCommentTimeMinusOffset(){
        date_default_timezone_set('America/Los_Angeles');
        sfContext::getInstance()->getUser()->setAttribute('system.timeZoneOffset', -4);
        $comment = new Comment();
        $comment->setCommentTime('2018-03-01 11:17:18');
        $commentTime = $comment->getCommentTime();
        $this->assertEquals('2018-03-01 15:17:18', $commentTime, 'comment time for client timezone is incorrect');
    }
}

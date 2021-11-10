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
 * RegistrationEventQueueDao Test Class
 * @group Core
 */
class RegistrationEventQueueDaoTest extends PHPUnit_Framework_TestCase
{
    private $registrationEventQueueDao;

    /**
     * Set up method
     */
    protected function setUp() {
        $this->registrationEventQueueDao = new RegistrationEventQueueDao();
    }

    public function testSaveRegistrationEventQueue() {

        $registrationEventQueue = new  RegistrationEventQueue();
        $registrationEventQueue->setEventType(0);
        $registrationEventQueue->setPublished(0);

        $eventTime=strtotime("2021-10-13");
        $registrationEventQueue->setEventTime(date("Y-m-d h:i:sa", $eventTime));

        $result = $this->registrationEventQueueDao->saveRegistrationEventQueue($registrationEventQueue);

        $this->assertTrue($result instanceof RegistrationEventQueue);
        $this->assertEquals(0, $result->getEventType());
    }

    public function testGetRegistrationEventQueueEventByType() {
        $result1 = $this->registrationEventQueueDao->getRegistrationEventQueueEventByType(0);
        $this->assertTrue($result1 instanceof RegistrationEventQueue);
        $this->assertEquals(0, $result1->getEventType());

        $result2 = $this->registrationEventQueueDao->getRegistrationEventQueueEventByType(6);
        $this->assertFalse($result2);
    }

    public function testGetUnpublishedRegistrationEventQueueEvents() {
        $registrationEventQueue = new  RegistrationEventQueue();
        $registrationEventQueue->setEventType(0);
        $registrationEventQueue->setPublished(0);
        $registrationEventQueue->save();
        $result1 = $this->registrationEventQueueDao->getUnpublishedRegistrationEventQueueEvents(1);
        $this->assertEquals(1, count($result1));
    }
}

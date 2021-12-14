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

namespace OrangeHRM\Tests\Core\Registration\Dao;

use DateTime;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Registration\Dao\RegistrationEventQueueDao;
use OrangeHRM\Entity\RegistrationEventQueue;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * Test class for RegistrationEventQueueDao
 * @group Core
 * @group Dao
 */
class RegistrationEventQueueDaoTest extends TestCase
{
    private RegistrationEventQueueDao $registrationEventQueueDao;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR) .
            '/orangehrmCorePlugin/test/fixtures/RegistrationEventQueueDao.yml';
        TestDataService::populate($fixture);
        $this->registrationEventQueueDao = new RegistrationEventQueueDao();
    }

    public function testGetRegistrationEventByType(): void
    {
        $event1 = $this->registrationEventQueueDao->getRegistrationEventByType(
            RegistrationEventQueue::INSTALLATION_START
        );
        $this->assertEquals(0, $event1->getEventType());
        $this->assertEquals(1, $event1->getId());

        $event2 = $this->registrationEventQueueDao->getRegistrationEventByType(
            RegistrationEventQueue::ACTIVE_EMPLOYEE_COUNT
        );
        $this->assertEquals(1, $event2->getEventType());
        $this->assertEquals(4, $event2->getId());
    }

    public function testGetUnpublishedRegistrationEvents(): void
    {
        $events1 = $this->registrationEventQueueDao->getUnpublishedRegistrationEvents(
            RegistrationEventQueue::PUBLISH_EVENT_BATCH_SIZE
        );
        $this->assertCount(4, $events1);

        $events2 = $this->registrationEventQueueDao->getUnpublishedRegistrationEvents(2);
        $this->assertCount(2, $events2);
        $this->assertEquals(0, $events2[0]->getEventType());
        $this->assertEquals(3, $events2[1]->getEventType());
        $this->assertEquals(1, $events2[0]->getId());
        $this->assertEquals(2, $events2[1]->getId());
    }

    public function testSaveRegistrationEvent(): void
    {
        $registrationEventQueue = new RegistrationEventQueue();
        $registrationEventQueue->setEventType(1);
        $registrationEventQueue->setEventTime(new DateTime());
        $registrationEventQueue->setPublishTime(new DateTime());
        $registrationEventQueue->setPublished(1);
        $registrationEventQueue->setData((['instance_identifier' => 'AHJVASKKJVKJHDBJASBAKJ']));
        $savedEvent = $this->registrationEventQueueDao->saveRegistrationEvent($registrationEventQueue);

        $this->assertEquals(['instance_identifier' => 'AHJVASKKJVKJHDBJASBAKJ'], $savedEvent->getData());
        $this->assertEquals(1, $savedEvent->getEventType());
    }
}

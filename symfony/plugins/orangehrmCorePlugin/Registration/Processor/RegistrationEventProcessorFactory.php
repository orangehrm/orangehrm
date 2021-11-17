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

namespace OrangeHRM\Core\Registration\Processor;

use OrangeHRM\Entity\RegistrationEventQueue;

class RegistrationEventProcessorFactory
{
    /**
     * @param string $eventType
     * @return AbstractRegistrationEventProcessor|null
     */
    public function getRegistrationEventProcessor(string $eventType): ?AbstractRegistrationEventProcessor
    {
        if ($eventType == RegistrationEventQueue::INSTALLATION_START) {
            return new RegistrationStartEventProcessor();
        } elseif ($eventType == RegistrationEventQueue::ACTIVE_EMPLOYEE_COUNT) {
            return new RegistrationEmployeeActivationEventProcessor();
        } elseif ($eventType == RegistrationEventQueue::INACTIVE_EMPLOYEE_COUNT) {
            return new RegistrationEmployeeTerminationEventProcessor();
        } elseif ($eventType == RegistrationEventQueue::INSTALLATION_SUCCESS) {
            return new RegistrationSuccessEventProcessor();
        } elseif ($eventType == RegistrationEventQueue::UPGRADE_START) {
            return null;
        }
        return null;
    }
}

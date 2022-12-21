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

namespace OrangeHRM\Core\Registration\Event;

use DateTime;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @Event("OrangeHRM\Core\Registration\Event\RegistrationEvent")
 */
class RegistrationEvent extends Event
{
    use DateTimeHelperTrait;

    public const EMPLOYEE_ADD_EVENT_NAME = 'registration.employee_add';
    public const EMPLOYEE_TERMINATE_EVENT_NAME = 'registration.employee_terminate';

    /**
     * @var DateTime
     */
    private DateTime $eventTime;

    public function __construct()
    {
        $this->eventTime = $this->getDateTimeHelper()->getNow();
    }

    /**
     * @return DateTime
     */
    public function getEventTime(): DateTime
    {
        return $this->eventTime;
    }
}

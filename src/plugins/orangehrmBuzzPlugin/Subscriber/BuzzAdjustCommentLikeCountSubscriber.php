<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Buzz\Subscriber;

use OrangeHRM\Buzz\Traits\Service\BuzzServiceTrait;
use OrangeHRM\Framework\Event\AbstractEventSubscriber;
use OrangeHRM\Maintenance\Event\MaintenanceEvent;
use OrangeHRM\Maintenance\Event\PurgeEmployee;
use OrangeHRM\Pim\Event\EmployeeDeletedEvent;
use OrangeHRM\Pim\Event\EmployeeEvents;

class BuzzAdjustCommentLikeCountSubscriber extends AbstractEventSubscriber
{
    use BuzzServiceTrait;

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            MaintenanceEvent::PURGE_EMPLOYEE_END => 'onEmployeePurgingEnd',
            EmployeeEvents::EMPLOYEES_DELETED => 'onEmployeesDeleted',
        ];
    }

    /**
     * @param PurgeEmployee $purgeEmployee
     */
    public function onEmployeePurgingEnd(PurgeEmployee $purgeEmployee): void
    {
        $this->getBuzzService()->getBuzzDao()->adjustLikeAndCommentCountsOnShares();
        $this->getBuzzService()->getBuzzDao()->adjustLikeCountOnComments();
    }

    /**
     * @param EmployeeDeletedEvent $event
     */
    public function onEmployeesDeleted(EmployeeDeletedEvent $event): void
    {
        $this->getBuzzService()->getBuzzDao()->adjustLikeAndCommentCountsOnShares();
        $this->getBuzzService()->getBuzzDao()->adjustLikeCountOnComments();
    }
}

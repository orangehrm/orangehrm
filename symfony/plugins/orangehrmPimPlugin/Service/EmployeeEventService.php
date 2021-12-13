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

namespace OrangeHRM\Pim\Service;

use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\EmployeeEvent;
use OrangeHRM\Entity\User;
use OrangeHRM\Pim\Dao\EmployeeEventDao;
use OrangeHRM\Pim\Dto\EmployeeEventSearchFilterParams;

class EmployeeEventService
{
    use UserRoleManagerTrait;
    use DateTimeHelperTrait;

    /**
     * @var EmployeeEventDao|null
     */
    private ?EmployeeEventDao $employeeEventDao = null;

    /**
     * @return EmployeeEventDao
     */
    public function getEmployeeEventDao(): EmployeeEventDao
    {
        if (!($this->employeeEventDao instanceof EmployeeEventDao)) {
            $this->employeeEventDao = new EmployeeEventDao();
        }

        return $this->employeeEventDao;
    }

    /**
     * @param EmployeeEventDao $employeeEventDao
     */
    public function setEmployeeEventDao(EmployeeEventDao $employeeEventDao): void
    {
        $this->employeeEventDao = $employeeEventDao;
    }

    /**
     * Saves a employee event
     *
     * To use in employee events.
     *
     * Save employee | Update contact details | Update dependents ...etc
     *
     * @param EmployeeEvent $employeeEvent
     * @return EmployeeEvent
     * @throws DaoException
     */
    public function saveEmployeeEvent(EmployeeEvent $employeeEvent): EmployeeEvent
    {
        return $this->getEmployeeEventDao()->saveEmployeeEvent($employeeEvent);
    }

    /**
     * Save employee event with parameters
     *
     * @param int $empNumber
     * @param string $type
     * @param string $event
     * @param string $note
     * @return EmployeeEvent
     * @throws DaoException
     */
    public function saveEvent(int $empNumber, string $type, string $event, string $note): EmployeeEvent
    {
        $employeeEvent = new EmployeeEvent();
        $employeeEvent->setEmpNumber($empNumber);
        $employeeEvent->setType($type);
        $employeeEvent->setEvent($event);
        $employeeEvent->setNote($note);
        $employeeEvent->setCreatedBy($this->getUserRole());
        $employeeEvent->setCreatedDate($this->getDateTimeHelper()->getNow());
        return $this->saveEmployeeEvent($employeeEvent);
    }

    /**
     * Get employee events
     *
     * @param EmployeeEventSearchFilterParams $employeeEventSearchFilterParams
     * @return EmployeeEvent[]
     * @throws DaoException
     */
    public function getEmployeeEvents(EmployeeEventSearchFilterParams $employeeEventSearchFilterParams): array
    {
        return $this->getEmployeeEventDao()->getEmployeeEvents($employeeEventSearchFilterParams);
    }

    /**
     * Get user role
     *
     * @return string
     */
    public function getUserRole(): string
    {
        $user = $this->getUserRoleManager()->getUser();
        if ($user instanceof User) {
            return $user->getUserRole()->getName();
        } else {
            return 'System';
        }
    }

    /**
     * @param int $empNumber
     * @return EmployeeEvent
     * @throws DaoException
     */
    public function saveAddEmployeeEvent(int $empNumber): EmployeeEvent
    {
        return $this->saveEvent(
            $empNumber,
            EmployeeEvent::EVENT_TYPE_EMPLOYEE,
            EmployeeEvent::EVENT_SAVE,
            'Saving Employee'
        );
    }

    /**
     * @param int $empNumber
     * @return EmployeeEvent
     * @throws DaoException
     */
    public function saveUpdateEmployeePersonalDetailsEvent(int $empNumber): EmployeeEvent
    {
        return $this->saveEvent(
            $empNumber,
            EmployeeEvent::EVENT_TYPE_EMPLOYEE,
            EmployeeEvent::EVENT_UPDATE,
            'Updating Employee Details'
        );
    }

    /**
     * @param int $empNumber
     * @return EmployeeEvent
     * @throws DaoException
     */
    public function saveUpdateJobDetailsEvent(int $empNumber): EmployeeEvent
    {
        return $this->saveEvent(
            $empNumber,
            EmployeeEvent::EVENT_TYPE_JOB_DETAIL,
            EmployeeEvent::EVENT_UPDATE,
            'Updating Employee Job Details'
        );
    }
}

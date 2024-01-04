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

namespace OrangeHRM\Entity\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use OrangeHRM\Entity\Employee;

class EmployeeListener extends BaseListener
{
    /**
     * @param Employee $employee
     * @param PreUpdateEventArgs $eventArgs
     */
    public function preUpdate(Employee $employee, PreUpdateEventArgs $eventArgs): void
    {
        if ($this->encryptionEnabled() && $eventArgs->hasChangedField('ssnNumber')) {
            $employee->setSsnNumber($this->getCryptographer()->encrypt($employee->getSsnNumber()));
        }
    }

    /**
     * @param Employee $employee
     * @param LifecycleEventArgs $eventArgs
     */
    public function postUpdate(Employee $employee, LifecycleEventArgs $eventArgs): void
    {
        if ($this->encryptionEnabled()) {
            $employee->setSsnNumber($this->getCryptographer()->decrypt($employee->getSsnNumber()));
        }
    }

    /**
     * @param Employee $employee
     * @param LifecycleEventArgs $eventArgs
     */
    public function postLoad(Employee $employee, LifecycleEventArgs $eventArgs): void
    {
        if ($this->encryptionEnabled()) {
            $employee->setSsnNumber($this->getCryptographer()->decrypt($employee->getSsnNumber()));
        }
    }
}

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

namespace OrangeHRM\Core\Api\V2\Validator\Rules;

use OrangeHRM\Core\Traits\UserRoleManagerTrait;

class InAccessibleEntityId extends AbstractRule
{
    use UserRoleManagerTrait;

    /**
     * @var string
     */
    private string $entityName;

    /**
     * @var InAccessibleEntityIdOption
     */
    private InAccessibleEntityIdOption $option;

    public function __construct(string $entityName, ?InAccessibleEntityIdOption $option = null)
    {
        $this->entityName = $entityName;
        $this->option = $option ?? new InAccessibleEntityIdOption();
    }


    /**
     * @param mixed $input
     * @return bool
     */
    public function validate($input): bool
    {
        if ($this->option->isNumeric() && !is_numeric($input)) {
            return false;
        } elseif ($this->option->isPositive() && !$input > 0) {
            return false;
        }

        $accessibleEntityIds = $this->getUserRoleManager()->getAccessibleEntityIds($this->entityName);
        return in_array($input, $accessibleEntityIds);
    }
}

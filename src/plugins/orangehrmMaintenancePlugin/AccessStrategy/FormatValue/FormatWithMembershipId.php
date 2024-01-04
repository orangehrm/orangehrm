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

namespace OrangeHRM\Maintenance\AccessStrategy\FormatValue;

use OrangeHRM\Admin\Service\MembershipService;
use OrangeHRM\Entity\Membership;
use OrangeHRM\Maintenance\FormatValueStrategy\ValueFormatter;

class FormatWithMembershipId implements ValueFormatter
{
    private ?MembershipService $membershipService = null;

    /**
     * @param $entityValue
     * @return string|null
     */
    public function getFormattedValue($entityValue): ?string
    {
        $memberShip = $this->getMembershipService()->getMembershipById($entityValue);
        if ($memberShip instanceof Membership) {
            return $memberShip->getName();
        }
        return null;
    }

    /**
     * @return MembershipService
     */
    public function getMembershipService(): MembershipService
    {
        if (is_null($this->membershipService)) {
            $this->membershipService = new MembershipService();
        }
        return $this->membershipService;
    }
}

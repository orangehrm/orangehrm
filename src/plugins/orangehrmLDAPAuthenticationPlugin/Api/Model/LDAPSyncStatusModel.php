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

namespace OrangeHRM\LDAP\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\LDAPSyncStatus;

/**
 * @OA\Schema(
 *     schema="LDAP-LDAPSyncStatusModel",
 *     type="object",
 *     @OA\Property(
 *         property="syncStartedAt",
 *         type="object",
 *         @OA\Property(property="date", type="string", format="date"),
 *         @OA\Property(property="time", type="string", format="time")
 *     ),
 *     @OA\Property(
 *         property="syncFinishedAt",
 *         type="object",
 *         @OA\Property(property="date", type="string", format="date"),
 *         @OA\Property(property="time", type="string", format="time")
 *     ),
 *     @OA\Property(property="syncStatus", type="string")
 * )
 */
class LDAPSyncStatusModel implements Normalizable
{
    use ModelTrait;

    public function __construct(LDAPSyncStatus $ldapSyncStatus)
    {
        $this->setEntity($ldapSyncStatus);
        $this->setFilters([
            ['getDecorator', 'getSyncStartedDate'],
            ['getDecorator', 'getSyncStartedTime'],
            ['getDecorator', 'getSyncFinishedDate'],
            ['getDecorator', 'getSyncFinishedTime'],
            'syncStatus'
        ]);

        $this->setAttributeNames([
            ['syncStartedAt','date'],
            ['syncStartedAt','time'],
            ['syncFinishedAt','date'],
            ['syncFinishedAt','time'],
            'syncStatus'
        ]);
    }
}

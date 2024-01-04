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

use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\LDAP\Dto\LDAPSetting;
use OrangeHRM\LDAP\Dto\LDAPUserLookupSetting;

/**
 * @OA\Schema(
 *     schema="LDAP-LDAPConfigModel",
 *     type="object",
 *     @OA\Property(property="enable", type="boolean"),
 *     @OA\Property(property="hostname", type="string"),
 *     @OA\Property(property="port", type="integer"),
 *     @OA\Property(property="encryption", type="string"),
 *     @OA\Property(property="ldapImplementation", type="string"),
 *     @OA\Property(property="bindAnonymously", type="boolean"),
 *     @OA\Property(property="bindUserDN", type="string", nullable=true),
 *     @OA\Property(property="hasBindUserPassword", type="boolean"),
 *     @OA\Property(property="userLookupSettings", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="dataMapping", type="object",
 *         @OA\Property(property="firstName", type="string"),
 *         @OA\Property(property="middleName", type="string", nullable=true),
 *         @OA\Property(property="lastName", type="string"),
 *         @OA\Property(property="workEmail", type="string", nullable=true),
 *         @OA\Property(property="employeeId", type="string", nullable=true),
 *         @OA\Property(property="userStatus", type="string", nullable=true)
 *     ),
 *     @OA\Property(property="mergeLDAPUsersWithExistingSystemUsers", type="boolean"),
 *     @OA\Property(property="syncInterval", type="integer")
 * )
 */
class LDAPConfigModel implements Normalizable
{
    /**
     * @var LDAPSetting
     */
    private LDAPSetting $ldapSetting;

    /**
     * @param LDAPSetting $ldapSetting
     */
    public function __construct(LDAPSetting $ldapSetting)
    {
        $this->ldapSetting = $ldapSetting;
    }


    public function toArray(): array
    {
        $userLookupSettings = array_map(
            fn (LDAPUserLookupSetting $lookupSetting) => $lookupSetting->toArray(),
            $this->ldapSetting->getUserLookupSettings()
        );
        return [
            'enable' => $this->ldapSetting->isEnable(),
            'hostname' => $this->ldapSetting->getHost(),
            'port' => $this->ldapSetting->getPort(),
            'encryption' => $this->ldapSetting->getEncryption(),
            'ldapImplementation' => $this->ldapSetting->getImplementation(),
            'bindAnonymously' => $this->ldapSetting->isBindAnonymously(),
            'bindUserDN' => $this->ldapSetting->getBindUserDN(),
            'hasBindUserPassword' => $this->ldapSetting->getBindUserPassword() !== null,
            'userLookupSettings' => $userLookupSettings,
            'dataMapping' => $this->ldapSetting->getDataMapping()->toArray(),
            'mergeLDAPUsersWithExistingSystemUsers' => $this->ldapSetting
                ->shouldMergeLDAPUsersWithExistingSystemUsers(),
            'syncInterval' => $this->ldapSetting->getSyncInterval()
        ];
    }
}

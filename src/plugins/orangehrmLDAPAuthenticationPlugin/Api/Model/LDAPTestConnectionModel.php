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

namespace OrangeHRM\LDAP\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\LDAP\Dto\LDAPSetting;
use OrangeHRM\LDAP\Service\LDAPTestService;
use OrangeHRM\LDAP\Service\LDAPTestSyncService;

class LDAPTestConnectionModel implements Normalizable
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

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $ldapTestService = new LDAPTestService($this->ldapSetting);
        $ldapTestSyncService = new LDAPTestSyncService($this->ldapSetting);
        return [
            [
                'category' => 'Login',
                'checks' => [
                    [
                        'label' => 'Authentication',
                        'value' => $ldapTestService->testAuthentication(),
                    ],
                ]
            ],
            // TODO
            [
                'category' => 'Lookup',
                'checks' => [
                    [
                        'label' => 'User lookup',
                        'value' => [
                            'message' => 'Ok',
                            'status' => 1
                        ]
                    ],
                    [
                        'label' => 'Search results',
                        'value' => [
                            'message' => $ldapTestSyncService->fetchEntryCollections()->count() . ' users found',
                            'status' => 1
                        ]
                    ],
                    [
                        'label' => 'Users',
                        'value' => [
                            'message' => \count($ldapTestSyncService->fetchAllLDAPUsers()->getLDAPUsers()) . ' users going to create',
                            'status' => 1
                        ]
                    ]
                ]
            ],
        ];
    }
}

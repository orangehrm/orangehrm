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

namespace OrangeHRM\Installer\Controller\Upgrader\Api;

use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Installer\Controller\AbstractInstallerRestController;
use OrangeHRM\Installer\Util\SystemConfig;

class SystemCheckAPI extends AbstractInstallerRestController
{
    /**
     * @inheritDoc
     */
    protected function handleGet(Request $request): array
    {
        $systemConfig = new SystemConfig();
        return [
            'data' => [
                [
                    'category' => 'Environment',
                    'checks' => [
                        [
                            'label' => 'PHP version',
                            'value' => $systemConfig->isPHPVersionCompatible()
                        ],
                        [
                            'label' => 'MYSQL Client',
                            'value' => $systemConfig->isMySqlClientCompatible()
                        ],
                        [
                            'label' => 'MYSQL Server',
                            'value' => $systemConfig->isMySqlServerCompatible()
                        ],
                        [
                            'label' => 'MYSQL InnoDB Support',
                            'value' => $systemConfig->isInnoDBSupport()
                        ],
                        [
                            'label' => 'Web Server',
                            'value' => $systemConfig->isWebServerCompatible()
                        ]
                    ]
                ],
                [
                    'category' => 'Permissions',
                    'checks' => [
                        [
                            'label' => 'Write Permissions for “lib/confs”',
                            'value' => $systemConfig->isWritableLibConfs()
                        ],
                        [
                            'label' => 'Write Permissions for “src/config”',
                            'value' => $systemConfig->isWritableSymfonyConfig()
                        ],
                        [
                            'label' => 'Write Permissions for “src/cache”',
                            'value' => $systemConfig->isWritableSymfonyCache()
                        ],
                        [
                            'label' => 'Write Permissions for “src/log”',
                            'value' => $systemConfig->isWritableSymfonyLog()
                        ],
                    ]
                ],
                [
                    'category' => 'Extensions',
                    'checks' => [
                        [
                            'label' => 'Maximum Session idle time before timeout',
                            'value' => $systemConfig->isMaximumSessionIdle()
                        ],
                        [
                            'label' => 'Register Global turned-off',
                            'value' => $systemConfig->isRegisterGlobalsOff()
                        ],
                        [
                            'label' => 'Memory Allocated for PHP script',
                            'value' => $systemConfig->getAllocatedMemoryStatus()
                        ],
                        [
                            'label' => 'cURL Status',
                            'value' => $systemConfig->isCurlEnabled()
                        ],
                        [
                            'label' => 'SimpleXML status',
                            'value' => $systemConfig->isSimpleXMLEnabled()
                        ],
                        [
                            'label' => 'Zip extension status',
                            'value' => $systemConfig->isZipExtensionEnabled()
                        ]
                    ]
                ]
            ],
            'meta' => [
                'isInterrupted' => $systemConfig->isInterruptContinue()
            ]
        ];
    }
}

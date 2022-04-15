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
use OrangeHRM\Framework\Http\Session\Session;
use OrangeHRM\Framework\ServiceContainer;
use OrangeHRM\Framework\Services;
use OrangeHRM\Installer\Controller\AbstractInstallerRestController;
use OrangeHRM\Installer\Controller\Upgrader\Traits\UpgraderUtilityTrait;
use OrangeHRM\Installer\Util\StateContainer;
use OrangeHRM\Installer\Util\Constant;

class DatabaseConfigAPI extends AbstractInstallerRestController
{
    use UpgraderUtilityTrait;

    /**
     * @inheritDoc
     */
    protected function handlePost(Request $request): array
    {
        $dbHost = $request->request->get('dbHost');
        $dbPort = $request->request->get('dbPort');
        $dbUser = $request->request->get('dbUser');
        $dbPassword = $request->request->get('dbPassword');
        $dbName = $request->request->get('dbName');

        StateContainer::getInstance()->storeDbInfo($dbHost, $dbPort, $dbUser, $dbPassword, $dbName);

        $connection = $this->checkDatabaseConnection();
        $response = $this->getResponse();
        if (!$connection) {
            $response->setStatusCode(400);
            return
                [
                    'error' => [
                        'status' => $response->getStatusCode(),
                        'message' => 'Failed to Connect: Check Database Details'
                    ]
                ];
        } elseif ($this->checkDatabaseStatus()) {
            $response->setStatusCode(400);
            return [
                'error' => [
                    'status' => $response->getStatusCode(),
                    'message' => 'Failed to Proceed: Interrupted Database'
                ]
            ];
        } else {
            return [
                'data' => [
                    'dbHost' => $dbHost,
                    'dbPort' => $dbPort,
                    'dbUser' => $dbUser,
                    'dbName' => $dbName,
                ],
                'meta' => []
            ];
        }
    }

    /**
     * @inheritDoc
     */
    protected function handleGet(Request $request): array
    {
        /** @var Session $session */
        $session = ServiceContainer::getContainer()->get(Services::SESSION);

        return [
            'data' => [
                'dbHost' => $session->get(Constant::DB_HOST),
                'dbPort' => $session->get(Constant::DB_PORT),
                'dbName' => $session->get(Constant::DB_NAME),
                'dbUser' => $session->get(Constant::DB_USER)
            ],
            'meta' => []

        ];
    }
}

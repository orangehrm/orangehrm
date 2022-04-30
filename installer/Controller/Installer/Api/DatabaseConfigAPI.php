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

namespace OrangeHRM\Installer\Controller\Installer\Api;

use Doctrine\DBAL\Exception;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\Installer\Controller\AbstractInstallerRestController;
use OrangeHRM\Installer\Util\AppSetupUtility;
use OrangeHRM\Installer\Util\StateContainer;

class DatabaseConfigAPI extends AbstractInstallerRestController
{
    /**
     * @inheritDoc
     */
    protected function handlePost(Request $request): array
    {
        $dbType = $request->request->get('dbType');
        $dbHost = $request->request->get('dbHost');
        $dbPort = $request->request->get('dbPort');
        $dbUser = $request->request->get('dbUser');
        $dbPassword = $request->request->get('dbPassword');
        $dbName = $request->request->get('dbName');

        if ($dbType === AppSetupUtility::INSTALLATION_DB_TYPE_EXISTING &&
            ($request->request->has('useSameDbUserForOrangeHRM') ||
                $request->request->has('ohrmDbUser') ||
                $request->request->has('ohrmDbPassword'))) {
            $this->getResponse()->setStatusCode(Response::HTTP_BAD_REQUEST);
            return [
                'error' => [
                    'status' => $this->getResponse()->getStatusCode(),
                    'message' => 'Unexpected Parameter `useSameDbUserForOrangeHRM` or `ohrmDbUser` or `ohrmDbPassword` Received'
                ]
            ];
        }

        $appSetupUtility = new AppSetupUtility();
        if ($dbType === AppSetupUtility::INSTALLATION_DB_TYPE_NEW) {
            $useSameDbUserForOrangeHRM = $request->request->getBoolean('useSameDbUserForOrangeHRM', false);
            $ohrmDbUser = $dbUser;
            $ohrmDbPassword = $dbPassword;
            if (!$useSameDbUserForOrangeHRM) {
                $ohrmDbUser = $request->request->get('ohrmDbUser');
                $ohrmDbPassword = $request->request->get('ohrmDbPassword');
            }

            StateContainer::getInstance()->storeDbInfo(
                $dbHost,
                $dbPort,
                new UserCredential($dbUser, $dbPassword),
                $dbName,
                new UserCredential($ohrmDbUser, $ohrmDbPassword)
            );
            StateContainer::getInstance()->setDbType(AppSetupUtility::INSTALLATION_DB_TYPE_NEW);

            $connection = $appSetupUtility->connectToDatabaseServer();
            if ($connection instanceof Exception) {
                $message = $appSetupUtility->getNewDBConnectionErrorMessage($connection, $dbHost, $dbPort);
                $this->getResponse()->setStatusCode(Response::HTTP_BAD_REQUEST);
                return [
                    'error' => [
                        'status' => $this->getResponse()->getStatusCode(),
                        'message' => $message
                    ]
                ];
            } elseif ($appSetupUtility->isDatabaseExist($dbName)) {
                $this->getResponse()->setStatusCode(Response::HTTP_BAD_REQUEST);
                return [
                    'error' => [
                        'status' => $this->getResponse()->getStatusCode(),
                        'message' => 'Database Already Exist'
                    ]
                ];
            } elseif (!$useSameDbUserForOrangeHRM && $appSetupUtility->isDatabaseUserExist($ohrmDbUser)) {
                $this->getResponse()->setStatusCode(Response::HTTP_BAD_REQUEST);
                return [
                    'error' => [
                        'status' => $this->getResponse()->getStatusCode(),
                        'message' => "Database User `$ohrmDbUser` Already Exist. Please Use Another Username for `OrangeHRM Database Username`."
                    ]
                ];
            } else {
                return [
                    'data' => [
                        'dbHost' => $dbHost,
                        'dbPort' => $dbPort,
                        'dbUser' => $dbUser,
                        'dbName' => $dbName,
                        'useSameDbUserForOrangeHRM' => $useSameDbUserForOrangeHRM,
                        'ohrmDbUser' => $useSameDbUserForOrangeHRM ? null : ($dbInfo[StateContainer::ORANGEHRM_DB_USER] ?? null),
                    ],
                    'meta' => []
                ];
            }
        }

        // `existing` database
        StateContainer::getInstance()->storeDbInfo(
            $dbHost,
            $dbPort,
            new UserCredential($dbUser, $dbPassword),
            $dbName
        );
        StateContainer::getInstance()->setDbType(AppSetupUtility::INSTALLATION_DB_TYPE_EXISTING);

        $connection = $appSetupUtility->connectToDatabase();
        if ($connection instanceof Exception) {
            $message = $appSetupUtility->getExistingDBConnectionErrorMessage($connection, $dbHost, $dbPort);
            $this->getResponse()->setStatusCode(Response::HTTP_BAD_REQUEST);
            return [
                'error' => [
                    'status' => $this->getResponse()->getStatusCode(),
                    'message' => $message
                ]
            ];
        } elseif (!$appSetupUtility->isExistingDatabaseEmpty()) {
            $this->getResponse()->setStatusCode(Response::HTTP_BAD_REQUEST);
            return [
                'error' => [
                    'status' => $this->getResponse()->getStatusCode(),
                    'message' => 'Provided Database Not Empty'
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
        $dbInfo = StateContainer::getInstance()->getDbInfo();
        $useSameDbUserForOrangeHRM = isset($dbInfo[StateContainer::ORANGEHRM_DB_USER]) && $dbInfo[StateContainer::DB_USER] == $dbInfo[StateContainer::ORANGEHRM_DB_USER];
        return [
            'data' => [
                'dbHost' => $dbInfo[StateContainer::DB_HOST],
                'dbPort' => $dbInfo[StateContainer::DB_PORT],
                'dbName' => $dbInfo[StateContainer::DB_NAME],
                'dbUser' => $dbInfo[StateContainer::DB_USER],
                'dbType' => StateContainer::getInstance()->getDbType(),
                'useSameDbUserForOrangeHRM' => $useSameDbUserForOrangeHRM,
                'ohrmDbUser' => $useSameDbUserForOrangeHRM ? null : ($dbInfo[StateContainer::ORANGEHRM_DB_USER] ?? null),
            ],
            'meta' => []
        ];
    }
}

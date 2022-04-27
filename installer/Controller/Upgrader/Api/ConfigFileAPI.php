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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use GuzzleHttp\Exception\GuzzleException;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\Installer\Controller\AbstractInstallerRestController;
use OrangeHRM\Installer\Util\AppSetupUtility;
use OrangeHRM\Installer\Util\DataRegistrationUtility;
use OrangeHRM\Installer\Util\StateContainer;
use OrangeHRM\Installer\Util\SystemConfig\SystemConfiguration;
use OrangeHRM\ORM\Doctrine;

class ConfigFileAPI extends AbstractInstallerRestController
{
    protected DataRegistrationUtility $dataRegistrationUtility;
    protected SystemConfiguration $systemConfiguration;

    public function __construct()
    {
        $this->dataRegistrationUtility = new DataRegistrationUtility();
        $this->systemConfiguration = new SystemConfiguration();
    }

    /**
     * @inheritDoc
     */
    protected function handlePost(Request $request): array
    {
        if (!StateContainer::getInstance()->isSetDbInfo()) {
            $this->getResponse()->setStatusCode(Response::HTTP_CONFLICT);
            return
                [
                    'error' => [
                        'status' => $this->getResponse()->getStatusCode(),
                        'message' => 'Database info not yet stored'
                    ]
                ];
        }

        $appSetupUtility = new AppSetupUtility();
        $appSetupUtility->writeConfFile();

        $this->sendRegistrationData();

        return [
            'success' => Doctrine::getEntityManager()->getConnection() instanceof Connection,
        ];
    }

    /**
     * @throws Exception
     * @throws GuzzleException
     */
    protected function sendRegistrationData(): void
    {
        $registrationType = $this->getRegistrationType();
        if (StateContainer::getInstance()->hasAttribute(DataRegistrationUtility::INITIAL_REGISTRATION_DATA_BODY)) {
            $this->systemConfiguration->setRegistrationEventQueue(
                $registrationType,
                DataRegistrationUtility::PUBLISHED,
                json_encode(
                    StateContainer::getInstance()->getAttribute(DataRegistrationUtility::INITIAL_REGISTRATION_DATA_BODY)
                )
            );
            StateContainer::getInstance()->removeAttribute(DataRegistrationUtility::INITIAL_REGISTRATION_DATA_BODY);
        } elseif (StateContainer::getInstance()->hasAttribute(DataRegistrationUtility::IS_INITIAL_REG_DATA_SENT)) {
            $this->dataRegistrationUtility->sendRegistrationDataOnFailure($registrationType);
        }
        //else initial registration data successfully sent at the beginning.

        $this->dataRegistrationUtility->sendRegistrationDataOnSuccess();
    }

    /**
     * @return int
     */
    protected function getRegistrationType(): int
    {
        return DataRegistrationUtility::REGISTRATION_TYPE_UPGRADER_STARTED;
    }
}

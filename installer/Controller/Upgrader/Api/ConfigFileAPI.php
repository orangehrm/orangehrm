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

namespace OrangeHRM\Installer\Controller\Upgrader\Api;

use Doctrine\DBAL\Connection;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\Installer\Controller\AbstractInstallerRestController;
use OrangeHRM\Installer\Util\AppSetupUtility;
use OrangeHRM\Installer\Util\DataRegistrationUtility;
use OrangeHRM\Installer\Util\Logger;
use OrangeHRM\Installer\Util\StateContainer;
use OrangeHRM\Installer\Util\SystemConfig\SystemConfiguration;
use OrangeHRM\ORM\Doctrine;
use Throwable;

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

        try {
            !StateContainer::getInstance()->getRegConsent() ?: $this->sendRegistrationData();
        } catch (Throwable $e) {
            Logger::getLogger()->error($e->getMessage());
            Logger::getLogger()->error($e->getTraceAsString());
        }

        $success = false;
        try {
            $success = Doctrine::getEntityManager()->getConnection() instanceof Connection;
        } catch (Throwable $e) {
            Logger::getLogger()->error($e->getMessage());
            Logger::getLogger()->error($e->getTraceAsString());
        }
        return ['success' => $success];
    }

    protected function sendRegistrationData(): void
    {
        $initialData = StateContainer::getInstance()->getInitialRegistrationData();
        $initialRegistrationDataBody = $initialData[StateContainer::INITIAL_REGISTRATION_DATA_BODY];
        $published = $initialData[StateContainer::IS_INITIAL_REG_DATA_SENT];
        $installerStartedEventStored = $initialData[StateContainer::INSTALLER_STARTED_EVENT_STORED];
        if (!$installerStartedEventStored) {
            $this->systemConfiguration->saveRegistrationEvent(
                $this->getRegistrationType(),
                $published,
                json_encode($initialRegistrationDataBody),
                $initialData[StateContainer::INSTALLER_STARTED_AT] ?? null
            );
        }

        if ($published) {
            $this->dataRegistrationUtility->sendRegistrationDataOnSuccess();
        } else {
            $successRegistrationDataBody = $this->dataRegistrationUtility->getSuccessRegistrationDataBody();
            $this->systemConfiguration->saveRegistrationEvent(
                DataRegistrationUtility::REGISTRATION_TYPE_SUCCESS,
                false,
                json_encode($successRegistrationDataBody)
            );
        }
    }

    /**
     * @return int
     */
    protected function getRegistrationType(): int
    {
        return DataRegistrationUtility::REGISTRATION_TYPE_UPGRADER_STARTED;
    }
}

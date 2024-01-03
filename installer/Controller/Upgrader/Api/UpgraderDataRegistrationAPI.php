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

use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Installer\Controller\AbstractInstallerRestController;
use OrangeHRM\Installer\Util\DataRegistrationUtility;
use OrangeHRM\Installer\Util\Service\DataRegistrationService;
use OrangeHRM\Installer\Util\StateContainer;
use OrangeHRM\Installer\Util\SystemConfig\SystemConfiguration;

class UpgraderDataRegistrationAPI extends AbstractInstallerRestController
{
    protected DataRegistrationService $dataRegistrationService;
    protected DataRegistrationUtility $dataRegistrationUtility;
    private SystemConfiguration $systemConfiguration;

    public function __construct()
    {
        $this->dataRegistrationService = new DataRegistrationService();
        $this->dataRegistrationUtility = new DataRegistrationUtility();
        $this->systemConfiguration = new SystemConfiguration();
    }

    /**
     * @inheritDoc
     */
    protected function handlePost(Request $request): array
    {
        $initialRegistrationDataBody = $this->dataRegistrationUtility->getInitialRegistrationDataBody(
            DataRegistrationUtility::REGISTRATION_TYPE_UPGRADER_STARTED
        );
        $published = $this->dataRegistrationService->sendRegistrationData($initialRegistrationDataBody);
        $upgraderStartedEventStored = false;
        if ($this->systemConfiguration->isRegistrationEventQueueAvailable()) {
            $this->systemConfiguration->saveRegistrationEvent(
                DataRegistrationUtility::REGISTRATION_TYPE_UPGRADER_STARTED,
                $published,
                json_encode($initialRegistrationDataBody)
            );
            $upgraderStartedEventStored = true;
        }
        StateContainer::getInstance()->storeInitialRegistrationData($initialRegistrationDataBody, $published, $upgraderStartedEventStored);

        $response = $this->getResponse();
        $message = $published ? 'Registration Data Sent Successfully!' : 'Failed To Send Registration Data';

        return [
            'status' => $response->getStatusCode(),
            'message' => $message
        ];
    }
}

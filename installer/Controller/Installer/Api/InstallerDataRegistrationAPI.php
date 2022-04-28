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

use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Installer\Controller\Upgrader\Api\UpgraderDataRegistrationAPI;
use OrangeHRM\Installer\Util\AppSetupUtility;
use OrangeHRM\Installer\Util\DataRegistrationUtility;
use OrangeHRM\Installer\Util\StateContainer;

class InstallerDataRegistrationAPI extends UpgraderDataRegistrationAPI
{
    private ?AppSetupUtility $appSetupUtility = null;

    public function __construct()
    {
        parent::__construct();
        $this->appSetupUtility = new AppSetupUtility();
    }

    /**
     * @inheritDoc
     */
    protected function handlePost(Request $request): array
    {
        list(
            $instanceIdentifier,
            $instanceIdentifierChecksum
            ) = $this->appSetupUtility->getInstanceUniqueIdentifyingData();
        StateContainer::getInstance()->storeInstanceIdentifierData($instanceIdentifier, $instanceIdentifierChecksum);

        $registrationType = $this->getRegistrationType();
        $this->dataRegistrationUtility->setInitialRegistrationDataBody($registrationType, $instanceIdentifier);
        $initialRegistrationDataBody = $this->dataRegistrationUtility->getInitialRegistrationDataBody();

        $result = $this->dataRegistrationService->sendRegistrationData($initialRegistrationDataBody);
        if (!$result) {
            StateContainer::getInstance()->setAttribute(
                DataRegistrationUtility::IS_INITIAL_REG_DATA_SENT,
                false
            );
        } else {
            StateContainer::getInstance()->setAttribute(
                DataRegistrationUtility::INITIAL_REGISTRATION_DATA_BODY,
                $initialRegistrationDataBody
            );
        }

        $response = $this->getResponse();
        $message = $result ? 'Registration Data Sent Successfully!' : 'Failed To Send Registration Data';

        return [
            'status' => $response->getStatusCode(),
            'message' => $message
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getRegistrationType(): int
    {
        return DataRegistrationUtility::REGISTRATION_TYPE_INSTALLER_STARTED;
    }
}

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
use OrangeHRM\Installer\Util\DataRegistrationUtility;
use OrangeHRM\Installer\Util\Service\DataRegistrationService;
use OrangeHRM\Installer\Util\SystemConfig\SystemConfiguration;

class SuccessDataRegistrationAPI extends AbstractInstallerRestController
{
    private SystemConfiguration $systemConfiguration;
    private DataRegistrationService $dataRegistrationService;

    public function __construct()
    {
        $this->systemConfiguration = new SystemConfiguration();
        $this->dataRegistrationService = new DataRegistrationService();
    }

    /**
     * @inheritDoc
     */
    protected function handlePost(Request $request): array
    {
        $result = $this->dataRegistrationService->sendSuccessRegistrationData(
            $this->systemConfiguration->getInstanceIdentifier(),
            DataRegistrationUtility::REGISTRATION_TYPE_UPGRADER_SUCCESS
        );
        $response = $this->getResponse();
        $message = $result ? 'Registration Data Sent Successfully!' : 'Failed To Send Registration Data';

        return [
            'status' => $response->getStatusCode(),
            'message' => $message
        ];
    }
}

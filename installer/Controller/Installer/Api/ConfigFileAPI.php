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

use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Installer\Util\DataRegistrationUtility;
use OrangeHRM\Installer\Util\StateContainer;

class ConfigFileAPI extends \OrangeHRM\Installer\Controller\Upgrader\Api\ConfigFileAPI
{
    /**
     * @inheritDoc
     */
    protected function handlePost(Request $request): array
    {
        if (StateContainer::getInstance()->isSetDbInfo()) {
            $dbInfo = StateContainer::getInstance()->getDbInfo();
            $dbUser = $dbInfo[StateContainer::ORANGEHRM_DB_USER] ?? $dbInfo[StateContainer::DB_USER];
            $dbPassword = isset($dbInfo[StateContainer::ORANGEHRM_DB_USER])
                ? $dbInfo[StateContainer::ORANGEHRM_DB_PASSWORD]
                : $dbInfo[StateContainer::DB_PASSWORD];
            StateContainer::getInstance()->storeDbInfo(
                $dbInfo[StateContainer::DB_HOST],
                $dbInfo[StateContainer::DB_PORT],
                new UserCredential($dbUser, $dbPassword),
                $dbInfo[StateContainer::DB_NAME]
            );
        }
        return parent::handlePost($request);
    }

    /**
     * @inheritDoc
     */
    protected function getRegistrationType(): int
    {
        return DataRegistrationUtility::REGISTRATION_TYPE_INSTALLER_STARTED;
    }
}

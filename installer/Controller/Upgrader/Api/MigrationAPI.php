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
use OrangeHRM\Installer\Util\AppSetupUtility;
use OrangeHRM\Installer\Util\Logger;

class MigrationAPI extends AbstractInstallerRestController
{
    /**
     * @inheritDoc
     */
    protected function handleGet(Request $request): array
    {
        $currentVersion = $request->query->get('currentVersion');
        $includeFromVersion = $request->query->getBoolean('includeFromVersion', false);
        $appSetupUtility = new AppSetupUtility();
        return $appSetupUtility->getVersionsInRange($currentVersion, null, $includeFromVersion);
    }

    /**
     * @inheritDoc
     */
    protected function handlePost(Request $request): array
    {
        $appSetupUtility = new AppSetupUtility();
        if ($request->request->has('version')) {
            $version = $request->request->get('version');
            $result = ['version' => $version];
            Logger::getLogger()->info(json_encode($result));
            $appSetupUtility->runMigrationFor($version);
            return $result;
        } else {
            $fromVersion = $request->request->get('fromVersion');
            $toVersion = $request->request->get('toVersion');
            $result = [
                'fromVersion' => $fromVersion,
                'toVersion' => $toVersion
            ];
            Logger::getLogger()->info(json_encode($result));
            $appSetupUtility->runMigrations($fromVersion, $toVersion);
            return $result;
        }
    }
}

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
use OrangeHRM\Installer\Util\AppSetupUtility;

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
            $appSetupUtility->runMigrationFor($version);
            return ['version' => $version];
        } else {
            $fromVersion = $request->request->get('fromVersion');
            $toVersion = $request->request->get('toVersion');
            $appSetupUtility->runMigrations($fromVersion, $toVersion);
            return [
                'fromVersion' => $fromVersion,
                'toVersion' => $toVersion
            ];
        }
    }
}

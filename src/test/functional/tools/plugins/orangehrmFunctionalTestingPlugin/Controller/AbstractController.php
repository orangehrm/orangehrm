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

namespace OrangeHRM\FunctionalTesting\Controller;

use OrangeHRM\Framework\Http\RedirectResponse;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\FunctionalTesting\Service\DatabaseBackupService;

class AbstractController extends \OrangeHRM\Core\Controller\AbstractController
{
    private ?DatabaseBackupService $databaseBackupService = null;

    /**
     * @return DatabaseBackupService
     */
    public function getDatabaseBackupService(): DatabaseBackupService
    {
        if (!$this->databaseBackupService instanceof DatabaseBackupService) {
            $this->databaseBackupService = new DatabaseBackupService();
        }
        return $this->databaseBackupService;
    }

    /**
     * @return RedirectResponse|Response
     */
    protected function getResponse()
    {
        $response = parent::getResponse();
        $response->headers->set(
            \OrangeHRM\Core\Api\V2\Response::CONTENT_TYPE_KEY,
            \OrangeHRM\Core\Api\V2\Response::CONTENT_TYPE_JSON
        );
        return $response;
    }
}

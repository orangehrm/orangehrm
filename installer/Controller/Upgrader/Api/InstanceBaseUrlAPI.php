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

use OrangeHRM\Core\Utility\InstanceBaseUrl;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\Installer\Controller\AbstractInstallerRestController;
use OrangeHRM\Installer\Util\AppSetupUtility;
use OrangeHRM\Installer\Util\Logger;
use OrangeHRM\Installer\Util\StateContainer;
use Throwable;

/**
 * Optional "Application URL" field (used in password reset emails). The value is kept in the installer
 * session and written to `hs_hr_config` (`instance.base_url`) by the config-file step.
 */
class InstanceBaseUrlAPI extends AbstractInstallerRestController
{
    public const PARAMETER_BASE_URL = 'baseUrl';

    protected ?AppSetupUtility $appSetupUtility = null;

    /**
     * @return AppSetupUtility
     */
    protected function getAppSetupUtility(): AppSetupUtility
    {
        return $this->appSetupUtility ??= new AppSetupUtility();
    }

    /**
     * Prefill: the value entered earlier in this session, else the one already stored in the database.
     * Nothing is derived from the request; the administrator enters the address.
     *
     * @inheritDoc
     */
    protected function handleGet(Request $request): array
    {
        $stored = $this->getStoredInstanceBaseUrl();
        if (StateContainer::getInstance()->hasInstanceBaseUrl()) {
            $baseUrl = StateContainer::getInstance()->getInstanceBaseUrl();
        } else {
            $baseUrl = $stored;
        }
        return [
            'data' => [
                self::PARAMETER_BASE_URL => $baseUrl,
                'stored' => $stored,
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    protected function handlePost(Request $request): array
    {
        $value = $request->request->all()[self::PARAMETER_BASE_URL] ?? null;
        if ($value === null || (is_string($value) && trim($value) === '')) {
            StateContainer::getInstance()->storeInstanceBaseUrl(null);
            return ['data' => [self::PARAMETER_BASE_URL => null]];
        }

        $baseUrl = is_string($value) ? InstanceBaseUrl::normalize($value) : null;
        if ($baseUrl === null) {
            $this->getResponse()->setStatusCode(Response::HTTP_BAD_REQUEST);
            return [
                'error' => [
                    'status' => $this->getResponse()->getStatusCode(),
                    'message' => 'Invalid `' . self::PARAMETER_BASE_URL . '`: expected ' . InstanceBaseUrl::DESCRIPTION,
                ],
            ];
        }
        StateContainer::getInstance()->storeInstanceBaseUrl($baseUrl);
        return ['data' => [self::PARAMETER_BASE_URL => $baseUrl]];
    }

    /**
     * @return string|null
     */
    protected function getStoredInstanceBaseUrl(): ?string
    {
        if (!StateContainer::getInstance()->isSetDbInfo()) {
            return null;
        }
        try {
            return $this->getAppSetupUtility()->getStoredInstanceBaseUrl();
        } catch (Throwable $e) {
            Logger::getLogger()->error($e->getMessage());
            return null;
        }
    }
}

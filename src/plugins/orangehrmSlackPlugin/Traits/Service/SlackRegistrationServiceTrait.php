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

namespace OrangeHRM\Slack\Traits\Service;

use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Services;
use OrangeHRM\Slack\Service\SlackRegistrationService;

/**
 * Container-backed accessor for {@see SlackRegistrationService}.
 *
 * The single shared instance is the key reason this exists: `SlackRegistrationModel::toArray()`
 * is invoked once per row in a collection response (`GET /registrations`), and instantiating
 * the service per call was wasteful. Now every model + API consumer goes through the same
 * container singleton.
 */
trait SlackRegistrationServiceTrait
{
    use ServiceContainerTrait;

    public function getSlackRegistrationService(): SlackRegistrationService
    {
        $container = $this->getContainer();
        if (!$container->has(Services::SLACK_REGISTRATION_SERVICE)) {
            $container->register(Services::SLACK_REGISTRATION_SERVICE, SlackRegistrationService::class);
        }
        return $container->get(Services::SLACK_REGISTRATION_SERVICE);
    }
}

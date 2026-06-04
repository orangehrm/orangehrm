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

namespace OrangeHRM\Slack\Traits\Dao;

use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Services;
use OrangeHRM\Slack\Dao\SlackLogDao;

/**
 * Container-backed accessor for {@see SlackLogDao}.
 *
 * Mirrors the per-service traits in {@see \OrangeHRM\Slack\Traits\Service} —
 * the DAO is registered once in {@see \SlackPluginConfiguration::initialize()}
 * and re-used by every caller. The defensive lazy-registration covers CLI
 * paths where the plugin init hook hasn't fired yet (console sub-processes,
 * test harnesses booting their own kernel, etc.).
 *
 * Why the DAO has a trait at all: the orchestrator needs to mock it in unit
 * tests. Direct `new SlackLogDao()` inside the service would make that
 * impossible. Routing through the container = tests register a mock in the
 * container, production code gets the real DAO — same code path either way.
 */
trait SlackLogDaoTrait
{
    use ServiceContainerTrait;

    public function getSlackLogDao(): SlackLogDao
    {
        $container = $this->getContainer();
        if (!$container->has(Services::SLACK_LOG_DAO)) {
            $container->register(Services::SLACK_LOG_DAO, SlackLogDao::class);
        }
        return $container->get(Services::SLACK_LOG_DAO);
    }
}

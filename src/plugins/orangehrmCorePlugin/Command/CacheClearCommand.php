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

namespace OrangeHRM\Core\Command;

use OrangeHRM\Core\Service\CacheService;
use OrangeHRM\Framework\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CacheClearCommand extends Command
{
    /**
     * @inheritDoc
     */
    public function getCommandName(): string
    {
        return 'cache:clear';
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $namespaces = ['orangehrm', 'doctrine_metadata', 'doctrine_queries'];
        $failed = false;
        foreach ($namespaces as $namespace) {
            $success = CacheService::getCache($namespace)->clear();
            if ($success) {
                $this->getIO()->success("Successfully cleared cache: `$namespace`");
                continue;
            }
            $failed = true;
            $this->getIO()->error("Failed to clear cache: `$namespace`");
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}

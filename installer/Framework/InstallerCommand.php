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

namespace OrangeHRM\Installer\Framework;

use OrangeHRM\Config\Config;
use OrangeHRM\Framework\Console\Command;
use OrangeHRM\Installer\Util\Logger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

abstract class InstallerCommand extends Command
{
    /**
     * @inheritDoc
     */
    public function run(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->setIO($input, $output);
            if (Config::isInstalled()) {
                $this->getIO()->warning('This system already installed.');
            }
            return parent::run($input, $output);
        } catch (Throwable $e) {
            Logger::getLogger()->error($e->getMessage());
            Logger::getLogger()->error($e->getTraceAsString());
            throw $e;
        }
    }
}

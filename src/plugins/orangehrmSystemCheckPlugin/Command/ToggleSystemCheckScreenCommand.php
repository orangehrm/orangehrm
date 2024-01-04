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

namespace OrangeHRM\SystemCheck\Command;

use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Framework\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ToggleSystemCheckScreenCommand extends Command
{
    use ConfigServiceTrait;

    /**
     * @inheritDoc
     */
    public function getCommandName(): string
    {
        return 'orangehrm:toggle-system-check-screen';
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->setDescription('Enable/disable the system check screen')
            ->addOption('status', null, InputOption::VALUE_NONE);
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('status') === true) {
            $status = $this->getConfigService()->showSystemCheckScreen()
                ? 'System check screen enabled'
                : 'System check screen disabled';
            $this->getIO()->note($status);
            return self::SUCCESS;
        }
        $currentStatus = $this->getConfigService()->showSystemCheckScreen();
        $this->getConfigService()->setShowSystemCheckScreen(!$currentStatus);

        $status = !$currentStatus ? 'Enabled' : 'Disabled';
        $this->getIO()->note("$status system check screen");
        return self::SUCCESS;
    }
}

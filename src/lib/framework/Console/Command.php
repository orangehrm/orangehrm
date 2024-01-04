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

namespace OrangeHRM\Framework\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class Command extends \Symfony\Component\Console\Command\Command
{
    protected ?SymfonyStyle $io = null;

    public function __construct()
    {
        parent::__construct($this->getCommandName());
    }

    /**
     * @return SymfonyStyle
     */
    protected function getIO(): SymfonyStyle
    {
        return $this->io;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function setIO(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * @inheritDoc
     */
    public function run(InputInterface $input, OutputInterface $output): int
    {
        $this->setIO($input, $output);
        return parent::run($input, $output);
    }

    /**
     * @return string
     */
    abstract public function getCommandName(): string;
}

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
 * Boston, MA 02110-1301, USA
 */

namespace OrangeHRM\FunctionalTesting\Command;

use OrangeHRM\Framework\Console\Command;
use OrangeHRM\FunctionalTesting\Service\DatabaseBackupService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDatabaseSavepointCommand extends Command
{
    /**
     * @inheritDoc
     */
    public function getCommandName(): string
    {
        return 'instance:create-db-savepoint';
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setDescription('Create database savepoint for given name')
            ->addArgument('savepointName', InputArgument::REQUIRED, 'Savepoint Name');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $databaseBackupService = new DatabaseBackupService();
        $savepointName = $input->getArgument('savepointName');
        $tables = $databaseBackupService->createSavepoint($savepointName);
        $this->getIO()->success(
            sprintf("Successfully created savepoint `$savepointName`, %d Tables dumped.", count($tables))
        );
        return self::SUCCESS;
    }
}

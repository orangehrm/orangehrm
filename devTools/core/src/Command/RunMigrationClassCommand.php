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

namespace OrangeHRM\DevTools\Command;

use Doctrine\DBAL\Connection;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Installer\Framework\HttpKernel;
use OrangeHRM\Installer\Util\ConfigHelper;
use OrangeHRM\Installer\Util\V1\AbstractMigration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RunMigrationClassCommand extends Command
{
    use EntityManagerHelperTrait;

    protected static $defaultName = 'migration:up';

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setDescription('Run given migration class')
            ->setHelp(
                'E.g. php devTools/core/console.php migration:up "\OrangeHRM\Installer\Migration\V5_2_0\Migration"'
            )
            ->addArgument('className', InputArgument::REQUIRED, 'Fully qualified class name');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        if (!Config::isInstalled()) {
            $io->warning('Application not installed.');
            return Command::INVALID;
        }

        $migrationClass = $input->getArgument('className');
        if (!is_subclass_of($migrationClass, AbstractMigration::class)) {
            $io->error("Invalid migration class `$migrationClass`");
            return Command::FAILURE;
        }
        new HttpKernel('prod', false); // Initiate kernel
        class_alias($migrationClass, '\OrangeHRM\DevTools\Command\_Migration');
        $migration = new class ($this->getEntityManager()->getConnection()) extends _Migration {
            private Connection $connection;

            public function __construct(Connection $connection)
            {
                $this->connection = $connection;
            }

            /**
             * @inheritDoc
             */
            public function getConnection(): Connection
            {
                return $this->connection;
            }
        };
        $migration->up();
        $configHelper = new ConfigHelper($migration->getConnection());
        $configHelper->setConfigValue('instance.version', $migration->getVersion());

        $io->success('Done');
        return Command::SUCCESS;
    }
}

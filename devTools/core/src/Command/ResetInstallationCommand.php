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
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\DevTools\Command;

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Utility\KeyHandler;
use OrangeHRM\Framework\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class ResetInstallationCommand extends Command
{
    use EntityManagerHelperTrait;

    protected static $defaultName = 'instance:reset';

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setDescription('Reset OrangeHRM installed instance');
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

        $pathToConf = Config::get(Config::CONF_FILE_PATH);
        $conf = Config::getConf();
        $dbName = $conf->getDbName();

        $sm = $this->getEntityManager()->getConnection()->createSchemaManager();
        $sm->dropDatabase("`$dbName`");
        $io->note("Dropped database `$dbName`");

        define('ENVIRONMENT', 'test');
        $testConf = Config::getConf(true);

        $fs = new Filesystem();
        $fs->remove($pathToConf);
        $io->note("Deleted conf file `$pathToConf`");

        $pathToKey = KeyHandler::getPathToKey();
        $fs->remove($pathToKey);
        $io->note("Deleted key.ohrm file `$pathToKey`");

        try {
            $sm->dropDatabase($testConf->getDbName());
            $io->note('Dropped test database');
        } catch (Throwable $e) {
        }

        $io->success('Done');
        return Command::SUCCESS;
    }
}

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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PHPFixCodingStandardsCommand extends Command
{
    protected static $defaultName = 'php-cs-fix';

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setDescription('Extend functionality of $ php ./devTools/core/vendor/bin/php-cs-fixer fix')
            ->setHelp('Exit with error status if some files fixed with this command')
            ->addOption('php', null, InputOption::VALUE_REQUIRED, '', 'php')
            ->addOption('reset-cache', 'f', InputOption::VALUE_NONE);
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $resetCache = $input->getOption('reset-cache');
        if ($resetCache) {
            $io->note('Requested cache reset');
            $cacheFile = realpath(__DIR__ . '/../../../../.php-cs-fixer.cache');
            if ($cacheFile) {
                $io->note('Found cache file');
                if (unlink($cacheFile)) {
                    $io->note('Cache file deleted');
                } else {
                    $io->note('Failed to cache file');
                }
            } else {
                $io->note('Cache file not found');
            }
        }
        $process = new Process(
            [$input->getOption('php'), './devTools/core/vendor/bin/php-cs-fixer', 'fix', '--format=json'],
            realpath(__DIR__ . '/../../../../')
        );
        try {
            $process->mustRun();

            $output = json_decode($process->getOutput(), true);
            if (!empty($output['files'])) {
                $io->table(
                    ['Fixed files'],
                    array_map(fn ($file) => [$file['name']], $output['files'])
                );
                return Command::FAILURE;
            }
        } catch (ProcessFailedException $exception) {
            $io->error($exception->getMessage());
            return Command::FAILURE;
        }
        $io->success('Done');
        return Command::SUCCESS;
    }
}

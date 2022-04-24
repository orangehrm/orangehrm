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

use OrangeHRM\DevTools\Command\Util\TranslationTestTool;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddTestTranslationCommand extends Command
{
    protected static $defaultName = 'add-test-translations';
    protected static $defaultDescription = 'Creates the translaltion yml files using the xml files.';

    private TranslationTestTool $translationTestTool;

    /**
     * @inheritDoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->translationTestTool = new TranslationTestTool();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->translationTestTool->setTestLanguage('zz_ZZZ');
        $modules = ['admin', 'general', 'pim', 'leave', 'time', 'attendance', 'maintenance', 'help', 'auth'];
        foreach ($modules as $module) {
            $this->translationTestTool->execute($module);
            $output->writeln('Added test translation strings to ' . $module . ' module strings.');
        }
        return Command::SUCCESS;
    }
}

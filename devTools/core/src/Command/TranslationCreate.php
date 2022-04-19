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

use OrangeHRM\DevTools\Command\Util\TranslationGenerateTool;
use OrangeHRM\Installer\Migration\V5_0_0\TranslationHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TranslationCreate extends Command
{
    protected static $defaultName = 'add-translations';
    protected static $defaultDescription = 'Creates the translaltion yml files using the xml files.';

    private TranslationGenerateTool $translationGenerator;

    private TranslationHelper $translationHelper;

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        // ...
    }

    /**
     * @inheritDoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->translationGenerator = new TranslationGenerateTool();

        $this->translationHelper = new TranslationHelper();
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $langCodes = ['bg_BG', 'da_DK', 'de', 'en_US', 'es', 'es_AR', 'es_BZ', 'es_CR', 'es_ES', 'fr', 'fr_FR', 'id_ID', 'ja_JP', 'nl', 'om_ET', 'th_TH', 'vi_VN', 'zh_Hans_CN', 'zh_Hant_TW'];  //add the xml files inside installer/upgrader/Migrations/V5/translations/messages folder

        foreach ($langCodes as $langCode) {
            $output->writeln('Creating yaml file for ' . $langCode);
            $this->translationGenerator->generateTranslations($langCode);
            $output->writeln([
                'Successfully Created',
                'Added language pack yml into installer/Migration/V5_0_0/translation directory',
            ]);
        }

        foreach ($langCodes as $langCode) {
            $this->translationHelper->addTranslations($langCode);
        }
        return Command::SUCCESS;
    }
}

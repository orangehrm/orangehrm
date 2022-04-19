<?php

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
    protected function configure(): void
    {
        // ...
    }

    /**
     * @inheritDoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->translationTestTool = new TranslationTestTool();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $modules = ['admin', 'general', 'pim', 'leave', 'time', 'attendance', 'maintenance', 'help', 'auth'];
        foreach ($modules as $module) {
            $this->translationTestTool->execute($module);
            $output->writeln('Added test translation strings to ' . $module . ' module strings.');
        }
        return Command::SUCCESS;
    }
}

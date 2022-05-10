<?php

namespace OrangeHRM\DevTools\Command;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\DevTools\Command\Util\AddLanguageStrings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddLangStrings extends Command
{
    use EntityManagerHelperTrait;

    protected static $defaultName = 'add-lang-strings';
    protected static $defaultDescription = 'Adds the lang strings for all defined langstrings in V5_1_0.';

    private AddLanguageStrings $addLanguageStrings;

    /**
     * @inheritDoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->addLanguageStrings = new AddLanguageStrings($this->getEntityManager()->getConnection());
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $groups = ['admin'];
        foreach ($groups as $group) {
            $this->addLanguageStrings->insertOrUpdateLangStrings($group);
        }
        return Command::SUCCESS;
    }
}

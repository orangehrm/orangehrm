<?php

namespace OrangeHRM\DevTools\Command;

use OrangeHRM\Installer\Util\AppSetupUtility;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupDefaultOrganization  extends Command
{
    protected static $defaultName = 'setup';

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setDescription('Setup with a default organization');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $data = [
            'organization' => [
                'name' => 'TranditPay',
                'countryCode' => 'GH'
            ],
            'department' => [
                'name' => 'Sales'
            ],
            'admin' => [
                'firstName' => 'Justice',
                'lastName' => 'Arthur',
                'workEmail' => 'justice@tranditpay.com',
                'contact' => '+233243742088',
                'username' => 'justicea83',
                'password' => 'password'
            ]
        ];

        AppSetupUtility::instance()->insertSystemConfiguration($data);
        return Command::SUCCESS;
    }
}
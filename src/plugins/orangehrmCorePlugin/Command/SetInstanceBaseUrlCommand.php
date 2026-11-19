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

namespace OrangeHRM\Core\Command;

use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Utility\InstanceBaseUrl;
use OrangeHRM\Framework\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Set the application base URL (`instance.base_url`) used in password reset emails, without editing the database.
 * Without an argument, show the URL that is stored.
 */
class SetInstanceBaseUrlCommand extends Command
{
    use ConfigServiceTrait;

    public const ARGUMENT_URL = 'url';

    /**
     * @inheritDoc
     */
    public function getCommandName(): string
    {
        return 'orangehrm:set-base-url';
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->setDescription('Set the application URL used in password reset emails')
            ->addArgument(
                self::ARGUMENT_URL,
                InputArgument::OPTIONAL,
                'Application URL: ' . InstanceBaseUrl::DESCRIPTION . '. Omit it to show the stored URL.'
            );
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $value = $input->getArgument(self::ARGUMENT_URL);
        if ($value === null) {
            $stored = $this->getConfigService()->getInstanceBaseUrl();
            if ($stored === null) {
                $this->getIO()->warning(
                    'No application URL is stored, so password reset emails are not sent. Set it with: ' .
                    'php bin/console ' . $this->getCommandName() . ' <url>'
                );
                return self::SUCCESS;
            }
            $this->getIO()->writeln("Application URL: $stored");
            return self::SUCCESS;
        }

        $baseUrl = $this->getConfigService()->setInstanceBaseUrl($value);
        if ($baseUrl === null) {
            $this->getIO()->error(
                'Invalid application URL. Expected ' . InstanceBaseUrl::DESCRIPTION . '. Nothing was changed.'
            );
            return self::INVALID;
        }
        $this->getIO()->success(
            "Application URL stored: $baseUrl\n" .
            'Password reset emails link to this address (`' . ConfigService::KEY_INSTANCE_BASE_URL . '`).'
        );
        return self::SUCCESS;
    }
}

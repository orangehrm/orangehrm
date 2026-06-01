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

namespace OrangeHRM\Slack\Command;

use OrangeHRM\Entity\SlackLog;
use OrangeHRM\Framework\Console\Command;
use OrangeHRM\Slack\Service\SlackNotificationService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class SendSlackNotificationsCommand extends Command
{
    public function getCommandName(): string
    {
        return 'orangehrm:send-slack-notifications';
    }

    protected function configure(): void
    {
        $this->setDescription('Send pending Slack notifications for active registrations');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $service = new SlackNotificationService();
            $summary = $service->dispatchDueNotifications();
        } catch (Throwable $e) {
            $this->getIO()->error('Slack scheduler failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        if (count($summary) === 0) {
            $this->getIO()->note('Slack notifications disabled or no active registrations');
            return self::SUCCESS;
        }

        $rows = [];
        $failed = 0;
        foreach ($summary as $registrationId => $entry) {
            $rows[] = [
                'registration' => (string)$registrationId,
                'status' => $entry['status'],
                'recipients' => (string)$entry['recipientCount'],
                'error' => $entry['error'] ?? '',
            ];
            if ($entry['status'] === SlackLog::STATUS_FAILED) {
                $failed++;
            }
        }
        $this->getIO()->table(['Registration', 'Status', 'Recipients', 'Error'], $rows);

        if ($failed > 0) {
            $this->getIO()->warning(sprintf('%d registration(s) failed.', $failed));
        } else {
            $this->getIO()->success('Slack notification run completed.');
        }
        return self::SUCCESS;
    }
}

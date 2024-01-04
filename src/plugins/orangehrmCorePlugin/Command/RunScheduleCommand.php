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

use DateTime;
use DateTimeZone;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Traits\ORM\EntityManagerTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\TaskSchedulerLog;
use OrangeHRM\Framework\Console\Command;
use OrangeHRM\Framework\Console\Scheduling\Schedule;
use OrangeHRM\Framework\Console\Scheduling\SchedulerConfigurationInterface;
use OrangeHRM\Framework\Logger\LoggerFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class RunScheduleCommand extends Command
{
    use DateTimeHelperTrait;
    use EntityManagerTrait;

    /**
     * @inheritDoc
     */
    public function getCommandName(): string
    {
        return 'orangehrm:run-schedule';
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setDescription('Running the scheduler');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->getOption('verbose')) {
            $output = new NullOutput();
            $this->setIO($input, $output);
        }
        $pluginConfigs = Config::get('ohrm_plugin_configs');
        $schedule = new Schedule($this->getApplication(), $output);
        foreach (array_values($pluginConfigs) as $pluginConfig) {
            $configClass = new $pluginConfig['classname']();
            if ($configClass instanceof SchedulerConfigurationInterface) {
                try {
                    $configClass->schedule($schedule);
                } catch (Throwable $e) {
                    $logger = LoggerFactory::getLogger('scheduler');
                    $logger->error($e->getMessage());
                    $logger->error($e->getTraceAsString());
                }
            }
        }

        $dueTasks = $schedule->getDueTasks(new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC));
        $this->getIO()->note('Time: ' . (new DateTime())->format('Y-m-d H:i:s O'));
        $this->getIO()->note('Event count: ' . count($dueTasks));
        foreach ($dueTasks as $task) {
            $taskLog = new TaskSchedulerLog();
            $taskLog->setStartedAt(
                $this->getDateTimeHelper()->getNow()
                    ->setTimezone(new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC))
            );
            $taskLog->setCommand($task->getCommand()->getCommand());
            $taskLog->setInput(
                $task->getCommand()->getInput() === null
                    ? null : $task->getCommand()->getInput()->getRawParameters()
            );
            $exitCode = $task->start();
            $taskLog->setFinishedAt(
                $this->getDateTimeHelper()->getNow()
                    ->setTimezone(new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC))
            );
            $taskLog->setStatus($exitCode);
            $this->getEntityManager()->persist($taskLog);
            $this->getEntityManager()->flush();
            $method = $exitCode === self::SUCCESS ? 'success' : 'error';
            $this->getIO()->$method($task->getCommand()->getCommand() . "; Exit code: $exitCode");
        }

        $this->getIO()->success('Scheduler success');
        return self::SUCCESS;
    }
}

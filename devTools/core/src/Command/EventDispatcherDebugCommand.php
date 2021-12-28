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

use OrangeHRM\Core\Traits\EventDispatcherTrait;
use OrangeHRM\Framework\Framework;
use OrangeHRM\Framework\Http\Request;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class EventDispatcherDebugCommand extends Command
{
    use EventDispatcherTrait;

    protected static $defaultName = 'debug:event-dispatcher';

    private SymfonyStyle $io;

    /**
     * @inheritDoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $kernel = new Framework('dev', false);
        $request = new Request();
        $kernel->handleRequest($request);

        foreach ($this->getEventDispatcher()->getListeners() as $event => $listeners) {
            $this->io->title((string)$event);
            $rows = [];
            foreach ($listeners as $i => $listener) {
                $listenerName = null;
                if (is_array($listener) && isset($listener[0])) {
                    $listenerName = get_class($listener[0]);
                    if (isset($listener[1])) {
                        $listenerName .= '::' . $listener[1];
                    }
                }
                $row = [
                    '#' . ($i + 1),
                    $listenerName,
                    $this->getEventDispatcher()->getListenerPriority($event, $listener)
                ];
                $rows[] = $row;
            }
            $this->io->table(['Order', 'Callable', 'Priority'], $rows);
        }
        return Command::SUCCESS;
    }
}

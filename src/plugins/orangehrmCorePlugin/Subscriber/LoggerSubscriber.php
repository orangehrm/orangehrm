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

namespace OrangeHRM\Core\Subscriber;

use Exception;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Event\AbstractEventSubscriber;
use OrangeHRM\Framework\Logger\Logger;
use OrangeHRM\Framework\Services;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LoggerSubscriber extends AbstractEventSubscriber
{
    use ServiceContainerTrait;

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => [
                ['onExceptionEvent', 100000],
            ],
        ];
    }

    /**
     * @param ExceptionEvent $event
     * @throws Exception
     */
    public function onExceptionEvent(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        /** @var Logger $logger */
        $logger = $this->getContainer()->get(Services::LOGGER);
        $logger->error($exception->getMessage());
        $logger->error($exception->getTraceAsString());
    }
}

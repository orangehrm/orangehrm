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
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Traits\LoggerTrait;
use OrangeHRM\Framework\Event\AbstractEventSubscriber;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class GlobalConfigSubscriber extends AbstractEventSubscriber
{
    use LoggerTrait;

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['onRequestEvent', 0],
            ],
        ];
    }

    /**
     * @param RequestEvent $event
     */
    public function onRequestEvent(RequestEvent $event)
    {
        $request = $event->getRequest();
        $this->setConfig(Config::I18N_ENABLED, $request->query->getBoolean('_i18nEnabled', true));
        $this->setConfig(Config::DATE_FORMATTING_ENABLED, $request->query->getBoolean('_dateFormattingEnabled', false));
    }

    /**
     * @param string $key
     * @param $value
     */
    private function setConfig(string $key, $value): void
    {
        try {
            Config::set($key, $value);
        } catch (Exception $e) {
            $this->getLogger()->error($e->getMessage());
        }
    }
}

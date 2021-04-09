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

use OrangeHRM\Core\Subscriber\ExceptionSubscriber;
use OrangeHRM\Core\Subscriber\LoggerSubscriber;
use OrangeHRM\Core\Subscriber\SessionSubscriber;
use OrangeHRM\Framework\PluginConfigurationInterface;
use OrangeHRM\Framework\ServiceContainer;
use OrangeHRM\Framework\Services;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpKernel\KernelEvents;

class CorePluginConfiguration implements PluginConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        $this->registerCoreSubscribers();

        $options = [
            'name' => '_orangehrm',
            // TODO:: enable this depend on request
            //'cookie_secure' => true,
            'cookie_httponly' => true,
            'cache_limiter' => 'nocache',
        ];
        $sessionStorage = new NativeSessionStorage($options, new NativeFileSessionHandler());
        $session = new Session($sessionStorage);
        $session->start();

        ServiceContainer::getContainer()->set(Services::SESSION_STORAGE, $sessionStorage);
        ServiceContainer::getContainer()->set(Services::SESSION, $session);
    }

    private function registerCoreSubscribers(): void
    {
        /** @var EventDispatcher $dispatcher */
        $dispatcher = ServiceContainer::getContainer()->get(Services::EVENT_DISPATCHER);
        $dispatcher->addSubscriber(new ExceptionSubscriber());
        $dispatcher->addSubscriber(new LoggerSubscriber());
        $dispatcher->addListener(
            KernelEvents::REQUEST,
            [
                new Symfony\Component\HttpKernel\EventListener\SessionListener(
                    ServiceContainer::getContainer()
                ),
                'onKernelRequest'
            ]
        );
        $dispatcher->addSubscriber(new SessionSubscriber());
    }
}

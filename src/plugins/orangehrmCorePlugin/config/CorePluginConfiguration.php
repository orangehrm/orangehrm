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

use OrangeHRM\Core\Authorization\Helper\UserRoleManagerHelper;
use OrangeHRM\Core\Authorization\Manager\UserRoleManagerFactory;
use OrangeHRM\Core\Helper\ClassHelper;
use OrangeHRM\Core\Registration\Subscriber\RegistrationEventPersistSubscriber;
use OrangeHRM\Core\Registration\Subscriber\RegistrationEventPublishSubscriber;
use OrangeHRM\Core\Service\CacheService;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Service\MenuService;
use OrangeHRM\Core\Service\NormalizerService;
use OrangeHRM\Core\Service\NumberHelperService;
use OrangeHRM\Core\Service\TextHelperService;
use OrangeHRM\Core\Subscriber\ApiAuthorizationSubscriber;
use OrangeHRM\Core\Subscriber\ExceptionSubscriber;
use OrangeHRM\Core\Subscriber\MailerSubscriber;
use OrangeHRM\Core\Subscriber\ModuleNotAvailableSubscriber;
use OrangeHRM\Core\Subscriber\RequestBodySubscriber;
use OrangeHRM\Core\Subscriber\RequestForwardableExceptionSubscriber;
use OrangeHRM\Core\Subscriber\ScreenAuthorizationSubscriber;
use OrangeHRM\Core\Subscriber\SessionSubscriber;
use OrangeHRM\Core\Traits\EventDispatcherTrait;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Session\NativeSessionStorage;
use OrangeHRM\Framework\Http\Session\Session;
use OrangeHRM\Framework\PluginConfigurationInterface;
use OrangeHRM\Framework\Services;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpKernel\EventListener\SessionListener;
use Symfony\Component\HttpKernel\KernelEvents;

class CorePluginConfiguration implements PluginConfigurationInterface
{
    use ServiceContainerTrait;
    use EventDispatcherTrait;

    /**
     * @inheritDoc
     */
    public function initialize(Request $request): void
    {
        $isSecure = $request->isSecure();
        $path = $request->getBasePath();
        $options = [
            'name' => $isSecure ? 'orangehrm' : '_orangehrm',
            'cookie_secure' => $isSecure,
            'cookie_httponly' => true,
            'cookie_path' => $path,
            'cookie_samesite' => 'Strict',
        ];
        $sessionStorage = new NativeSessionStorage($options, new NativeFileSessionHandler());
        $session = new Session($sessionStorage);
        $session->start();

        $this->getContainer()->set(Services::SESSION_STORAGE, $sessionStorage);
        $this->getContainer()->set(Services::SESSION, $session);
        $this->getContainer()->register(Services::CONFIG_SERVICE, ConfigService::class);
        $this->getContainer()->register(Services::NORMALIZER_SERVICE, NormalizerService::class);
        $this->getContainer()->register(Services::DATETIME_HELPER_SERVICE, DateTimeHelperService::class);
        $this->getContainer()->register(Services::TEXT_HELPER_SERVICE, TextHelperService::class);
        $this->getContainer()->register(Services::NUMBER_HELPER_SERVICE, NumberHelperService::class);
        $this->getContainer()->register(Services::CLASS_HELPER, ClassHelper::class);
        $this->getContainer()->register(Services::USER_ROLE_MANAGER)
            ->setFactory([UserRoleManagerFactory::class, 'getUserRoleManager']);
        $this->getContainer()->register(Services::USER_ROLE_MANAGER_HELPER, UserRoleManagerHelper::class);
        $this->getContainer()->register(Services::CACHE)->setFactory([CacheService::class, 'getCache']);
        $this->getContainer()->register(Services::MENU_SERVICE, MenuService::class);

        $this->registerCoreSubscribers();
    }

    private function registerCoreSubscribers(): void
    {
        $this->getEventDispatcher()->addSubscriber(new ExceptionSubscriber());
        $this->getEventDispatcher()->addListener(
            KernelEvents::REQUEST,
            [new SessionListener($this->getContainer()), 'onKernelRequest'],
        );
        $this->getEventDispatcher()->addSubscriber(new SessionSubscriber());
        $this->getEventDispatcher()->addSubscriber(new RequestForwardableExceptionSubscriber());
        $this->getEventDispatcher()->addSubscriber(new ScreenAuthorizationSubscriber());
        $this->getEventDispatcher()->addSubscriber(new ApiAuthorizationSubscriber());
        $this->getEventDispatcher()->addSubscriber(new RequestBodySubscriber());
        $this->getEventDispatcher()->addSubscriber(new MailerSubscriber());
        $this->getEventDispatcher()->addSubscriber(new ModuleNotAvailableSubscriber());
        $this->getEventDispatcher()->addSubscriber(new RegistrationEventPersistSubscriber());
        $this->getEventDispatcher()->addSubscriber(new RegistrationEventPublishSubscriber());
    }
}

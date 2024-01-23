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

use OrangeHRM\Authentication\Auth\AuthProviderChain;
use OrangeHRM\Authentication\Auth\LocalAuthProvider;
use OrangeHRM\Authentication\Auth\User as AuthUser;
use OrangeHRM\Authentication\Csrf\CsrfTokenManager;
use OrangeHRM\Authentication\Service\AuthenticationService;
use OrangeHRM\Authentication\Service\PasswordStrengthService;
use OrangeHRM\Authentication\Subscriber\AdministratorAccessSubscriber;
use OrangeHRM\Authentication\Subscriber\AuthenticationSubscriber;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Event\EventDispatcher;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\PluginConfigurationInterface;
use OrangeHRM\Framework\Services;
use Symfony\Component\Security\Csrf\TokenStorage\NativeSessionTokenStorage;

class AuthenticationPluginConfiguration implements PluginConfigurationInterface
{
    use ServiceContainerTrait;

    /**
     * @inheritDoc
     */
    public function initialize(Request $request): void
    {
        /** @var EventDispatcher $dispatcher */
        $dispatcher = $this->getContainer()->get(Services::EVENT_DISPATCHER);
        $dispatcher->addSubscriber(new AuthenticationSubscriber());
        $dispatcher->addSubscriber(new AdministratorAccessSubscriber());
        $this->getContainer()->register(Services::AUTH_USER)
            ->setFactory([AuthUser::class, 'getInstance']);

        $this->getContainer()->register(Services::CSRF_TOKEN_STORAGE, NativeSessionTokenStorage::class);
        $this->getContainer()->register(Services::CSRF_TOKEN_MANAGER, CsrfTokenManager::class);
        /** @var AuthProviderChain $authProviderChain */
        $authProviderChain = $this->getContainer()->get(Services::AUTH_PROVIDER_CHAIN);
        $authProviderChain->addProvider(new LocalAuthProvider());

        $this->getContainer()->register(Services::PASSWORD_STRENGTH_SERVICE, PasswordStrengthService::class);
        $this->getContainer()->register(Services::AUTHENTICATION_SERVICE, AuthenticationService::class);
    }
}

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

namespace OrangeHRM\OpenidAuthentication\Controller;

use Jumbojett\OpenIDConnectClientException;
use OrangeHRM\Core\Authorization\Service\HomePageService;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Controller\PublicControllerInterface;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Framework\Http\RedirectResponse;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Session\MemorySessionStorage;
use OrangeHRM\Framework\Http\Session\Session;
use OrangeHRM\Framework\ServiceContainer;
use OrangeHRM\Framework\Services;
use OrangeHRM\OpenidAuthentication\OpenID\OpenIDConnectClient;
use OrangeHRM\OpenidAuthentication\Traits\Service\SocialMediaAuthenticationServiceTrait;

class OpenIdConnectLoginController extends AbstractVueController implements PublicControllerInterface
{
    use AuthUserTrait;
    use SocialMediaAuthenticationServiceTrait;

    public const SCOPE = 'email';
    public const REDIRECT_URL = 'https://734d-2402-d000-a500-40f9-f1e8-1109-5f81-bcf4.ngrok-free.app/orangehrm5/web/index.php/openidauth/openIdCredentials';
    private bool $isAuthenticated = false;

    /**
     * @var HomePageService|null
     */
    protected ?HomePageService $homePageService = null;

    /**
     * @return HomePageService
     */
    public function getHomePageService(): HomePageService
    {
        if (!$this->homePageService instanceof HomePageService) {
            $this->homePageService = new HomePageService();
        }
        return $this->homePageService;
    }

    /**
     * @throws OpenIDConnectClientException
     */
    public function handle(Request $request): RedirectResponse
    {
        $response = $this->getResponse();
        $providerId = $request->attributes->get('providerId');
        $oidcClient = new OpenIDConnectClient();

        if ($providerId > 0) {
            $this->setSession($providerId);
            $provider = $this->getSocialMediaAuthenticationService()->getAuthProviderDao()
                ->getAuthProviderDetailsByProviderId($providerId);

            $oidcClient = $this->getSocialMediaAuthenticationService()->initiateAuthentication(
                $provider,
                self::SCOPE,
                self::REDIRECT_URL
            );

            $this->isAuthenticated = $oidcClient->authenticate();
        }

        if ($this->isAuthenticated) {
            $provider = $this->getSocialMediaAuthenticationService()->getAuthProviderDao()
                ->getAuthProviderDetailsByProviderId(1);

            $oidcClient->setProviderURL($provider->getOpenIdProvider()->getProviderUrl());

            $email = $oidcClient->requestUserInfo('email');
        }

        return new RedirectResponse($oidcClient->getGeneratedAuthUrl());
    }

    private function setSession($providerId)
    {
        $sessionStorage = new MemorySessionStorage();
        ServiceContainer::getContainer()->set(Services::SESSION_STORAGE, $sessionStorage);
        $session = new Session($sessionStorage);
        $session->start();
        ServiceContainer::getContainer()->set(Services::SESSION, $session);
        $session->set('providerId', $providerId);
    }
}

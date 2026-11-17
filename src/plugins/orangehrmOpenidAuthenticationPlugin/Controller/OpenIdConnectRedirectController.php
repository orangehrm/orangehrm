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

use Exception;
use Jumbojett\OpenIDConnectClientException;
use OrangeHRM\Authentication\Auth\User as AuthUser;
use OrangeHRM\Authentication\Controller\Traits\SessionHandlingTrait;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Authentication\Exception\AuthenticationException;
use OrangeHRM\Authentication\Service\LoginService;
use OrangeHRM\Core\Authorization\Service\HomePageService;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Controller\PublicControllerInterface;
use OrangeHRM\Core\Exception\RedirectableException;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\OpenIdProvider;
use OrangeHRM\Framework\Http\RedirectResponse;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Routing\UrlGenerator;
use OrangeHRM\Framework\Services;
use OrangeHRM\I18N\Traits\Service\I18NHelperTrait;
use OrangeHRM\OpenidAuthentication\Exception\OpenIdConnectAuthenticationException;
use OrangeHRM\OpenidAuthentication\Traits\Service\SocialMediaAuthenticationServiceTrait;
use Throwable;

class OpenIdConnectRedirectController extends AbstractVueController implements PublicControllerInterface
{
    use AuthUserTrait;
    use I18NHelperTrait;
    use SessionHandlingTrait;
    use EntityManagerHelperTrait;
    use SocialMediaAuthenticationServiceTrait;

    /**
     * @var HomePageService|null
     */
    protected ?HomePageService $homePageService = null;

    /**
     * @var null|LoginService
     */
    protected ?LoginService $loginService = null;

    /**
     * @return HomePageService
     */
    public function getHomePageService(): HomePageService
    {
        return $this->homePageService ??= new HomePageService();
    }

    /**
     * @return LoginService
     */
    public function getLoginService(): LoginService
    {
        return $this->loginService ??= new LoginService();
    }

    /**
     * @throws Exception
     */
    public function handle(Request $request): RedirectResponse
    {
        /** @var UrlGenerator $urlGenerator */
        $urlGenerator = $this->getContainer()->get(Services::URL_GENERATOR);
        $loginUrl = $urlGenerator->generate('auth_login', [], UrlGenerator::ABSOLUTE_URL);

        try {
            $this->beginTransaction();
            $providerId = $this->getAuthUser()->getAttribute(AuthUser::OPENID_PROVIDER_ID);
            $this->getAuthUser()->removeAttribute(AuthUser::OPENID_PROVIDER_ID);
            $authProvider = is_int($providerId)
                ? $this->getSocialMediaAuthenticationService()->getAuthProviderDao()->getAuthProviderById($providerId)
                : null;
            if (!$authProvider instanceof OpenIdProvider || !$authProvider->getStatus()) {
                throw OpenIdConnectAuthenticationException::providerNotAvailable();
            }
            $authProviderExtraDetails = $this->getSocialMediaAuthenticationService()->getAuthProviderDao()
                ->getAuthProviderDetailsByProviderId($providerId);

            $oidcClient = $this->getSocialMediaAuthenticationService()->initiateAuthentication(
                $authProviderExtraDetails,
                $this->getSocialMediaAuthenticationService()->getScope(),
                $this->getSocialMediaAuthenticationService()->getRedirectURL()
            );

            try {
                if (!$oidcClient->authenticate()) {
                    throw new OpenIDConnectClientException('authenticate() returned false');
                }
                $idTokenClaims = $oidcClient->getVerifiedClaims();
                $userInfo = $oidcClient->requestUserInfo();
            } catch (OpenIDConnectClientException $e) {
                // e.g. the user cancelled at the provider, or the state, token or userinfo response was invalid
                $this->getSocialMediaAuthenticationService()->logRefusal($authProvider, $e->getMessage());
                throw OpenIdConnectAuthenticationException::signInFailed($authProvider->getProviderName());
            }

            $userCredentials = new UserCredential();
            $userCredentials->setUsername(
                $this->getSocialMediaAuthenticationService()->getVerifiedEmail($idTokenClaims, $userInfo, $authProvider)
            );

            // From here on the local account is involved: one message for every refusal, so that a sign-in
            // attempt does not reveal whether an account exists for the email
            try {
                // Refuses deleted, disabled, terminated and employee-less users before anything is recorded
                $user = $this->getSocialMediaAuthenticationService()->getUserForAuthenticate($userCredentials);

                // Record the sign-in before any user attribute is written to the session
                $this->getLoginService()->addOIDCLogin($user);
                $this->commitTransaction();

                if (!$this->getSocialMediaAuthenticationService()->handleOIDCAuthentication($user)) {
                    throw AuthenticationException::invalidCredentials();
                }
            } catch (AuthenticationException $e) {
                $this->getSocialMediaAuthenticationService()->logRefusal(
                    $authProvider,
                    'local account refused: ' . $e->getMessage()
                );
                throw OpenIdConnectAuthenticationException::accountNotAvailable();
            }
            $this->getAuthUser()->setIsAuthenticated(true);

            $redirectUrl = $this->handleSessionTimeoutRedirect();
            if ($redirectUrl) {
                return new RedirectResponse($redirectUrl);
            }

            $homePagePath = $this->getHomePageService()->getHomePagePath();
            return $this->redirect($homePagePath);
        } catch (AuthenticationException $e) {
            $this->rollBackIfActive();
            $this->getAuthUser()->addFlash(AuthUser::FLASH_LOGIN_ERROR, $e->normalize());
            if ($e instanceof RedirectableException) {
                return new RedirectResponse($e->getRedirectUrl());
            }
            return new RedirectResponse($loginUrl);
        } catch (Throwable $e) {
            $this->rollBackIfActive();
            $this->getAuthUser()->addFlash(
                AuthUser::FLASH_LOGIN_ERROR,
                [
                    'error' => AuthenticationException::UNEXPECT_ERROR,
                    'message' => 'Unexpected error occurred',
                ]
            );
            return new RedirectResponse($loginUrl);
        }

    }

    private function rollBackIfActive(): void
    {
        if ($this->getEntityManager()->getConnection()->isTransactionActive()) {
            $this->rollBackTransaction();
        }
    }
}

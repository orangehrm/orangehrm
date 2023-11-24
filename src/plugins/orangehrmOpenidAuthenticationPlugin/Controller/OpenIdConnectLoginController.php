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

namespace OrangeHRM\OpenidAuthentication\Controller;

use Jumbojett\OpenIDConnectClientException;
use OrangeHRM\Core\Authorization\Service\HomePageService;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Controller\PublicControllerInterface;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Services;
use OrangeHRM\OpenidAuthentication\Auth\OpenIdConnectAuthProvider;
use OrangeHRM\OpenidAuthentication\Service\SocialMediaAuthenticationService;

class OpenIdConnectLoginController extends AbstractVueController implements PublicControllerInterface
{
    use AuthUserTrait;

    public const SCOPE = 'email';
    public const REDIRECT_URL = '/auth/oidcLogin';

    /**
     * @var SocialMediaAuthenticationService|null
     */
    protected ?SocialMediaAuthenticationService $socialMediaAuthenticationService = null;

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
     * @return SocialMediaAuthenticationService
     */
    public function getSocialMediaAuthenticationService(): SocialMediaAuthenticationService
    {
        if (!$this->socialMediaAuthenticationService instanceof SocialMediaAuthenticationService){
            $this->socialMediaAuthenticationService = new SocialMediaAuthenticationService();
        }
        return $this->socialMediaAuthenticationService;
    }

    /**
     * @throws OpenIDConnectClientException
     */
    public function handle(Request $request)
    {
        $response = $this->getResponse();
        $providerId = $request->request->get('providerId');
        $provider = $this->getSocialMediaAuthenticationService()->getAuthProviderDao()
            ->getAuthProviderDetailsByProviderId($providerId);

        $oidcClient = $this->getSocialMediaAuthenticationService()->initiateAuthentication(
            $provider,
            self::SCOPE,
            self::REDIRECT_URL
        );

        $email = $this->getSocialMediaAuthenticationService()->handleCallback($oidcClient);

        /** @var OpenIdConnectAuthProvider $authProvider */
        $authProvider = $this->getContainer()->get(Services::OPENID_CONNECT_PROVIDER);
        $success = $authProvider->authenticate($email);

        if ($success) {
            if ($this->getAuthUser()->isAuthenticated()) {
                $homePagePath = $this->getHomePageService()->getHomePagePath();
                return $this->redirect($homePagePath);
            }
            return parent::handle($request);
        } else {
            //handle error
        }
        return $response;
    }
}

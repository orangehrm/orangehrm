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
use OrangeHRM\Core\Authorization\Service\HomePageService;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Controller\PublicControllerInterface;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Framework\Http\RedirectResponse;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\OpenidAuthentication\Traits\Service\SocialMediaAuthenticationServiceTrait;

class OpenIdConnectRedirectController extends AbstractVueController implements PublicControllerInterface
{
    use AuthUserTrait;
    use SocialMediaAuthenticationServiceTrait;

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

    //TODO

    /**
     * @throws OpenIDConnectClientException
     * @throws Exception
     */
    public function handle(Request $request): RedirectResponse
    {
        //TODO
        $provider = $this->getSocialMediaAuthenticationService()->getAuthProviderDao()
            ->getAuthProviderDetailsByProviderId(1);

        $oidcClient = $this->getSocialMediaAuthenticationService()->initiateAuthentication(
            $provider,
            $this->getSocialMediaAuthenticationService()->getScope(),
            $this->getSocialMediaAuthenticationService()->getRedirectURL()
        );

        $oidcClient->authenticate();

        $homePagePath = $this->getHomePageService()->getHomePagePath();
        return $this->redirect($homePagePath);
    }
}

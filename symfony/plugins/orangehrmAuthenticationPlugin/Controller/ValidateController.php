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

namespace OrangeHRM\Authentication\Controller;

use OrangeHRM\Authentication\Auth\User as AuthUser;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Authentication\Service\AuthenticationService;
use OrangeHRM\Core\Authorization\Service\HomePageService;
use OrangeHRM\Core\Controller\AbstractController;
use OrangeHRM\Core\Controller\PublicControllerInterface;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Http\RedirectResponse;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Session\Session;
use OrangeHRM\Framework\Routing\UrlGenerator;
use OrangeHRM\Framework\Services;

class ValidateController extends AbstractController implements PublicControllerInterface
{
    use AuthUserTrait;
    use ServiceContainerTrait;

    public const PARAMETER_USERNAME = 'username';
    public const PARAMETER_PASSWORD = 'password';

    /**
     * @var AuthenticationService|null
     */
    protected ?AuthenticationService $authenticationService = null;

    /**
     * @var HomePageService|null
     */
    protected ?HomePageService $homePageService = null;

    /**
     * @return AuthenticationService
     */
    public function getAuthenticationService(): AuthenticationService
    {
        if (!$this->authenticationService instanceof AuthenticationService) {
            $this->authenticationService = new AuthenticationService();
        }
        return $this->authenticationService;
    }

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

    public function handle(Request $request): RedirectResponse
    {
        /** @var UrlGenerator $urlGenerator */
        $urlGenerator = $this->getContainer()->get(Services::URL_GENERATOR);

        $username = $request->get(self::PARAMETER_USERNAME, '');
        $password = $request->get(self::PARAMETER_PASSWORD, '');

        $credentials = new UserCredential($username, $password);
        $success = $this->getAuthenticationService()->setCredentials($credentials, []);
        $this->getAuthUser()->setIsAuthenticated($success);
        $loginUrl = $urlGenerator->generate('auth_login', [], UrlGenerator::ABSOLUTE_URL);
        $logoutUrl = $urlGenerator->generate('auth_logout', [], UrlGenerator::ABSOLUTE_URL);

        if (!$success) {
            return new RedirectResponse($loginUrl);
        }

        /** @var Session $session */
        $session = $this->getContainer()->get(Services::SESSION);
        if ($session->has(AuthUser::SESSION_TIMEOUT_REDIRECT_URL)) {
            $redirectUrl = $session->get(AuthUser::SESSION_TIMEOUT_REDIRECT_URL);
            $session->remove(AuthUser::SESSION_TIMEOUT_REDIRECT_URL);
            if ($redirectUrl !== $loginUrl || $redirectUrl !== $logoutUrl) {
                return new RedirectResponse($redirectUrl);
            }
        }

        // TODO: Redirect to user homepage (using homepage service)
        return new RedirectResponse(
            $urlGenerator->generate('view_job_title', [], UrlGenerator::ABSOLUTE_URL)
        );
    }
}

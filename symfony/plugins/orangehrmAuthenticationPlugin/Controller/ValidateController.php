<?php

namespace OrangeHRM\Authentication\Controller;

use OrangeHRM\Authentication\Auth\User;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Authentication\Service\AuthenticationService;
use OrangeHRM\Core\Authorization\Service\HomePageService;
use OrangeHRM\Core\Controller\AbstractController;
use OrangeHRM\Core\Controller\PublicControllerInterface;
use OrangeHRM\Framework\ServiceContainer;
use OrangeHRM\Framework\Services;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ValidateController extends AbstractController implements PublicControllerInterface
{
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
        $urlGenerator = ServiceContainer::getContainer()->get(Services::URL_GENERATOR);

        $username = $request->get(self::PARAMETER_USERNAME, '');
        $password = $request->get(self::PARAMETER_PASSWORD, '');

        $credentials = new UserCredential($username, $password);
        $success = $this->getAuthenticationService()->setCredentials($credentials, []);
        User::getInstance()->setIsAuthenticated($success);
        $loginUrl = $urlGenerator->generate('auth_login', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $logoutUrl = $urlGenerator->generate('auth_logout', [], UrlGeneratorInterface::ABSOLUTE_URL);

        if (!$success) {
            return new RedirectResponse($loginUrl);
        }

        /** @var Session $session */
        $session = ServiceContainer::getContainer()->get(Services::SESSION);
        if ($session->has('redirect_uri')) {
            $redirectUrl = $session->get('redirect_uri');
            $session->remove('redirect_uri');
            if ($redirectUrl !== $loginUrl || $redirectUrl !== $logoutUrl) {
                return new RedirectResponse($redirectUrl);
            }
        }

        // TODO: Redirect to user homepage (using homepage service)
        return new RedirectResponse(
            $urlGenerator->generate('view_job_title', [], UrlGeneratorInterface::ABSOLUTE_URL)
        );
    }
}

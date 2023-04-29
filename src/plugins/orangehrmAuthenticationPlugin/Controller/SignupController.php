<?php

namespace OrangeHRM\Authentication\Controller;

use Exception;
use OrangeHRM\Admin\Service\OrganizationService;
use OrangeHRM\Authentication\Auth\User as AuthUser;
use OrangeHRM\Authentication\Dto\OrganizationSetup;
use OrangeHRM\Authentication\Exception\AuthenticationException;
use OrangeHRM\Authentication\Traits\CsrfTokenManagerTrait;
use OrangeHRM\Core\Authorization\Service\HomePageService;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Http\RedirectResponse;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Core\Controller\AbstractController;
use OrangeHRM\Core\Controller\PublicControllerInterface;
use OrangeHRM\Framework\Routing\UrlGenerator;
use OrangeHRM\Framework\Services;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Throwable;

class SignupController extends AbstractController implements PublicControllerInterface
{
    use AuthUserTrait;
    use ServiceContainerTrait;
    use CsrfTokenManagerTrait;

    public const PARAMETER_ORG_NAME = 'organizationName';
    public const PARAMETER_COUNTRY_CODE = 'countryCode';
    public const PARAMETER_FIRSTNAME = 'firstName';
    public const PARAMETER_LASTNAME = 'lastName';
    public const PARAMETER_EMAIL = 'email';
    public const PARAMETER_PASSWORD = 'password';
    public const PARAMETER_CONFIRM_PASSWORD = 'confirmPassword';

    protected ?OrganizationService $organizationService = null;

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

    public function getOrganizationService(): ?OrganizationService
    {
        if (!$this->organizationService instanceof OrganizationService) {
            $this->organizationService = new OrganizationService();
        }
        return $this->organizationService;
    }


    /**
     * @throws Exception
     */
    public function handle(Request $request): RedirectResponse
    {
        $organizationName = $request->request->get(self::PARAMETER_ORG_NAME, '');
        $countryCode = $request->request->get(self::PARAMETER_COUNTRY_CODE, '');
        $firstName = $request->request->get(self::PARAMETER_FIRSTNAME, '');
        $lastName = $request->request->get(self::PARAMETER_LASTNAME, '');
        $email = $request->request->get(self::PARAMETER_EMAIL, '');
        $password = $request->request->get(self::PARAMETER_PASSWORD, '');
        $confirmPassword = $request->request->get(self::PARAMETER_CONFIRM_PASSWORD, '');

        /** @var UrlGenerator $urlGenerator */
        $urlGenerator = $this->getContainer()->get(Services::URL_GENERATOR);
        $registerUrl = $urlGenerator->generate('auth_register', [], UrlGeneratorInterface::ABSOLUTE_URL);

        try {
            $token = $request->request->get('_token');
            if (!$this->getCsrfTokenManager()->isValid('register', $token)) {
                throw AuthenticationException::invalidCsrfToken();
            }

            $this->getOrganizationService()->setupNewOrganization(
                OrganizationSetup::instance()->setCountryCode($countryCode)
                    ->setEmail($email)
                    ->setLastName($lastName)
                    ->setFirstName($firstName)
                    ->setPassword($password)
                    ->setOrganizationName($organizationName)
            );

        } catch (AuthenticationException $e) {
            $this->getAuthUser()->addFlash(AuthUser::FLASH_REGISTRATION_ERROR, $e->normalize());
            return new RedirectResponse($registerUrl);
        } catch (Throwable) {
            $this->getAuthUser()->addFlash(
                AuthUser::FLASH_REGISTRATION_ERROR,
                [
                    'error' => AuthenticationException::UNEXPECT_ERROR,
                    'message' => 'Unexpected error occurred',
                ]
            );
            return new RedirectResponse($registerUrl);
        }
        $this->getAuthUser()->addFlash(
            AuthUser::FLASH_SUCCESS_REGISTRATION,
            [
                'message' => 'Registration Completed successfully, login now!',
            ]
        );

        return $this->redirect('/auth/login');
    }
}
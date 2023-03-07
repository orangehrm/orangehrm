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

namespace OrangeHRM\OAuth\Subscriber;

use Exception;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use OrangeHRM\Admin\Traits\Service\UserServiceTrait;
use OrangeHRM\Authentication\Service\AuthenticationService;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerTrait;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Entity\OAuthAccessToken;
use OrangeHRM\Framework\Event\AbstractEventSubscriber;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\Framework\Services;
use OrangeHRM\OAuth\Dto\CryptKey;
use OrangeHRM\OAuth\Repository\AccessTokenRepository;
use OrangeHRM\OAuth\Server\BearerTokenValidator;
use OrangeHRM\OAuth\Traits\PsrHttpFactoryHelperTrait;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OAuthSubscriber extends AbstractEventSubscriber
{
    use AuthUserTrait;
    use PsrHttpFactoryHelperTrait;
    use EntityManagerTrait;
    use UserServiceTrait;
    use ConfigServiceTrait;

    private AuthenticationService $authenticationService;

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onRequestEvent', 98000]],
            KernelEvents::RESPONSE => [['onResponseEvent', 0]],
        ];
    }

    /**
     * @return AuthenticationService
     */
    private function getAuthenticationService(): AuthenticationService
    {
        return $this->authenticationService ??= new AuthenticationService();
    }

    /**
     * @param RequestEvent $event
     */
    public function onRequestEvent(RequestEvent $event): void
    {
        if (!$this->getAuthUser()->isAuthenticated() && $event->getRequest()->headers->has('authorization')) {
            // Attempt to check OAuth token

            $encryptionKey = $this->getConfigService()->getOAuthEncryptionKey();
            $accessTokenRepository = new AccessTokenRepository();
            $server = new ResourceServer(
                $accessTokenRepository,
                new CryptKey(),
                new BearerTokenValidator($accessTokenRepository, $encryptionKey),
            );

            try {
                $request = $this->getPsrHttpFactoryHelper()->createPsr7Request($event->getRequest());
                $request = $server->validateAuthenticatedRequest($request);

                // TODO:: refactor
                /** @var OAuthAccessToken $accessToken */
                $accessToken = $this->getEntityManager()
                    ->getRepository(OAuthAccessToken::class)
                    ->findOneBy(['accessToken' => $request->getAttribute(BearerTokenValidator::ATTRIBUTE_ACCESS_TOKEN)]);

                $user = $this->getUserService()->geUserDao()->getSystemUser($accessToken->getUserId());

                $this->getAuthenticationService()->setCredentialsForUser($user);
                $this->getAuthUser()->setIsAuthenticated(true);
            } catch (OAuthServerException $e) {
                // TODO
                $e->generateHttpResponse($this->getPsrHttpFactoryHelper()->createPsr7Response(new Response()));
                throw $e;
            } catch (Exception $e) {
                // TODO
                throw $e;
            }
        }
    }

    /**
     * @param ResponseEvent $event
     */
    public function onResponseEvent(ResponseEvent $event): void
    {
        // TODO:: use memory session storage
        if ($event->getRequest()->headers->has('authorization') && $this->getAuthUser()->isAuthenticated()) {
            $session = $this->getContainer()->get(Services::SESSION);
            $session->invalidate();
        }
    }
}

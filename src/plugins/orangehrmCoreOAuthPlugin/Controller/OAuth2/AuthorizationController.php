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

namespace OrangeHRM\OAuth\Controller\OAuth2;

use DateInterval;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use Nyholm\Psr7\Factory\Psr17Factory;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Traits\LoggerTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\OAuth\Dto\Entity\UserEntity;
use OrangeHRM\OAuth\Repository\AccessTokenRepository;
use OrangeHRM\OAuth\Repository\AuthorizationCodeRepository;
use OrangeHRM\OAuth\Repository\ClientRepository;
use OrangeHRM\OAuth\Repository\RefreshTokenRepository;
use OrangeHRM\OAuth\Repository\ScopeRepository;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

class AuthorizationController extends AbstractVueController
{
    use LoggerTrait;
    use UserRoleManagerTrait;

    public function handle(Request $request)
    {
        $clientRepository = new ClientRepository();
        $scopeRepository = new ScopeRepository();
        $accessTokenRepository = new AccessTokenRepository();
        $authCodeRepository = new AuthorizationCodeRepository();
        $refreshTokenRepository = new RefreshTokenRepository();

        $privateKey = "-----BEGIN PRIVATE KEY-----
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
KabyuWN8fRssa+rTCAhXZVvOnnBZRQ==
-----END PRIVATE KEY-----";
        $privateKey = new CryptKey($privateKey); // if private key has a pass phrase
        $encryptionKey = 'lxZFUEsBCJ2Yb14IF2ygAHI5N4+ZAUXXaSeeJm6+twsUmIen'; // generate using base64_encode(random_bytes(32))

        // Setup the authorization server
        $server = new AuthorizationServer(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            $privateKey,
            $encryptionKey
        );

        $grant = new AuthCodeGrant(
            $authCodeRepository,
            $refreshTokenRepository,
            new DateInterval('PT10M') // authorization codes will expire after 10 minutes
        );

        $grant->setRefreshTokenTTL(new DateInterval('P1M')); // refresh tokens will expire after 1 month

        // Enable the authentication code grant on the server
        $server->enableGrantType(
            $grant,
            new DateInterval('PT1H') // access tokens will expire after 1 hour
        );

        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $psrRequest = $psrHttpFactory->createRequest($request);


        try {

            // Validate the HTTP request and return an AuthorizationRequest object.
            $authRequest = $server->validateAuthorizationRequest($psrRequest);

            // The auth request object can be serialized and saved into a user's session.
            // You will probably want to redirect the user at this point to a login endpoint.

            $user = UserEntity::createFromEntity($this->getUserRoleManager()->getUser());
            // Once the user has logged in set the user on the AuthorizationRequest
            $authRequest->setUser($user); // an instance of UserEntityInterface

            // At this point you should redirect the user to an authorization page.
            // This form will ask the user to approve the client and the scopes requested.

            // Once the user has approved or denied the client update the status
            // (true = approved, false = denied)
            $authRequest->setAuthorizationApproved(true);


            $response = $this->getResponse();

            $psr17Factory = new Psr17Factory();
            $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
            $psrResponse = $psrHttpFactory->createResponse($response);
            $psrResponse = $server->completeAuthorizationRequest($authRequest, $psrResponse);

            $httpFoundationFactory = new HttpFoundationFactory();
            return $httpFoundationFactory->createResponse($psrResponse);
        } catch (OAuthServerException $exception) {
            $response = $this->getResponse();

            $psr17Factory = new Psr17Factory();
            $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
            $psrResponse = $psrHttpFactory->createResponse($response);

            $httpFoundationFactory = new HttpFoundationFactory();
            // All instances of OAuthServerException can be formatted into a HTTP response
            return $httpFoundationFactory->createResponse($exception->generateHttpResponse($psrResponse));
        } catch (\Throwable $exception) {
            // TODO
            return $this->getResponse()
                ->setStatusCode(500)
                ->setContent($exception->getMessage().'<br>'.$exception->getTraceAsString());
        }


        return $this->getResponse();
    }
}

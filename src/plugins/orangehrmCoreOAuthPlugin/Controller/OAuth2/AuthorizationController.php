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

namespace OrangeHRM\OAuth\Controller\OAuth2;

use League\OAuth2\Server\Exception\OAuthServerException;
use OrangeHRM\Core\Controller\AbstractViewController;
use OrangeHRM\Core\Traits\LoggerTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\OAuth\Dto\Entity\UserEntity;
use OrangeHRM\OAuth\Traits\OAuthServerTrait;
use OrangeHRM\OAuth\Traits\PsrHttpFactoryHelperTrait;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuthorizationController extends AbstractViewController
{
    use LoggerTrait;
    use UserRoleManagerTrait;
    use OAuthServerTrait;
    use PsrHttpFactoryHelperTrait;

    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        try {
            $server = $this->getOAuthServer()->getServer();
            $psrRequest = $this->getPsrHttpFactoryHelper()->createPsr7Request($request);
            $authRequest = $server->validateAuthorizationRequest($psrRequest);

            $user = UserEntity::createFromEntity($this->getUserRoleManager()->getUser());
            $authRequest->setUser($user);

            $authorized = filter_var(
                $psrRequest->getQueryParams()['authorized'],
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            );
            $authRequest->setAuthorizationApproved($authorized === true);

            $psrResponse = $this->getPsrHttpFactoryHelper()->createPsr7Response($this->getResponse());
            $psrResponse = $server->completeAuthorizationRequest($authRequest, $psrResponse);

            return $this->getPsrHttpFactoryHelper()->createResponseFromPsr7Response($psrResponse);
        } catch (OAuthServerException $e) {
            $psrResponse = $this->getPsrHttpFactoryHelper()->createPsr7Response($this->getResponse());
            return $this->getPsrHttpFactoryHelper()
                ->createResponseFromPsr7Response($e->generateHttpResponse($psrResponse));
        } catch (Throwable $e) {
            $psrResponse = $this->getPsrHttpFactoryHelper()->createPsr7Response($this->getResponse());
            return $this->getPsrHttpFactoryHelper()
                ->createResponseFromPsr7Response(
                    (OAuthServerException::serverError('An unexpected error has occurred'))
                        ->generateHttpResponse($psrResponse)
                );
        }
    }
}

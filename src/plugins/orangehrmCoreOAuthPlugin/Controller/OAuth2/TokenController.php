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

use League\OAuth2\Server\Exception\OAuthServerException;
use OrangeHRM\Core\Controller\AbstractController;
use OrangeHRM\Core\Controller\PublicControllerInterface;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\OAuth\Traits\OAuthServerTrait;
use OrangeHRM\OAuth\Traits\PsrHttpFactoryHelperTrait;
use Throwable;

class TokenController extends AbstractController implements PublicControllerInterface
{
    use OAuthServerTrait;
    use PsrHttpFactoryHelperTrait;

    public function handle(Request $request)
    {
        try {
            $server = $this->getOAuthServer()->getServer();
            $psrRequest = $this->getPsrHttpFactoryHelper()->createPsr7Request($request);
            $psrResponse = $this->getPsrHttpFactoryHelper()->createPsr7Response($this->getResponse());
            $psrResponse = $server->respondToAccessTokenRequest($psrRequest, $psrResponse);

            return $this->getPsrHttpFactoryHelper()->createResponseFromPsr7Response($psrResponse);
        } catch (OAuthServerException $e) {
            $psrResponse = $this->getPsrHttpFactoryHelper()->createPsr7Response($this->getResponse());
            return $this->getPsrHttpFactoryHelper()
                ->createResponseFromPsr7Response($e->generateHttpResponse($psrResponse));
        } catch (Throwable $e) {
            // TODO
            return $this->getResponse()
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

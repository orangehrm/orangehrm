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

namespace OrangeHRM\OAuth\Controller;

use League\OAuth2\Server\Exception\OAuthServerException;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\OAuth\Traits\OAuthServerTrait;
use OrangeHRM\OAuth\Traits\PsrHttpFactoryHelperTrait;
use Throwable;

class AuthorizationController extends AbstractVueController
{
    use OAuthServerTrait;
    use PsrHttpFactoryHelperTrait;

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $psrRequest = $this->getPsrHttpFactoryHelper()->createPsr7Request($request);
        try {
            $server = $this->getOAuthServer()->getServer();
            $authRequest = $server->validateAuthorizationRequest($psrRequest);
        } catch (OAuthServerException $e) {
            dd($e->getMessage());
            // TODO
            $psrResponse = $this->getPsrHttpFactoryHelper()->createPsr7Response($this->getResponse());
//            return $this->getPsrHttpFactoryHelper()
//                ->createResponseFromPsr7Response($e->generateHttpResponse($psrResponse));
        } catch (Throwable $e) {
            dd($e->getMessage());
            // TODO
//            return $this->getResponse()
//                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $component = new Component('oauth-authorize');
        $component->addProp(new Prop('params', Prop::TYPE_OBJECT, $psrRequest->getQueryParams()));
        $component->addProp(new Prop('client-name', Prop::TYPE_STRING, $authRequest->getClient()->getDisplayName())); // TODO
        $this->setComponent($component);
        $this->setTemplate('no_header.html.twig');
    }
}

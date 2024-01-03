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

namespace OrangeHRM\OAuth\Service;

use Nyholm\Psr7\Factory\Psr17Factory;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;

class PsrHttpFactoryHelper
{
    private ?HttpMessageFactoryInterface $psrHttpFactory = null;
    private HttpFoundationFactoryInterface $httpFoundationFactory;

    /**
     * @return PsrHttpFactory
     */
    protected function getPsrHttpFactory(): PsrHttpFactory
    {
        if (!$this->psrHttpFactory instanceof PsrHttpFactory) {
            $psr17Factory = new Psr17Factory();
            $this->psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        }
        return $this->psrHttpFactory;
    }

    /**
     * @return HttpFoundationFactory
     */
    protected function getHttpFoundationFactory(): HttpFoundationFactory
    {
        return $this->httpFoundationFactory ??= new HttpFoundationFactory();
    }

    /**
     * Creates a PSR-7 Request instance from a Symfony one.
     *
     * @param \Symfony\Component\HttpFoundation\Request|Request $request
     * @return ServerRequestInterface
     */
    public function createPsr7Request(Request $request): ServerRequestInterface
    {
        return $this->getPsrHttpFactory()->createRequest($request);
    }

    /**
     * Creates a PSR-7 Response instance from a Symfony one.
     *
     * @param \Symfony\Component\HttpFoundation\Response|Response $response
     * @return ResponseInterface
     */
    public function createPsr7Response($response): ResponseInterface
    {
        return $this->getPsrHttpFactory()->createResponse($response);
    }

    /**
     * Creates a Symfony Request instance from a PSR-7 one.
     *
     * @param ServerRequestInterface $psrRequest
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function createRequestFromPsr7Request(
        ServerRequestInterface $psrRequest
    ): \Symfony\Component\HttpFoundation\Request {
        return $this->getHttpFoundationFactory()->createRequest($psrRequest, false);
    }

    /**
     * Creates a Symfony Response instance from a PSR-7 one.
     *
     * @param ResponseInterface $psrResponse
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createResponseFromPsr7Response(
        ResponseInterface $psrResponse
    ): \Symfony\Component\HttpFoundation\Response {
        return $this->getHttpFoundationFactory()->createResponse($psrResponse, false);
    }
}

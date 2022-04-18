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

namespace OrangeHRM\Core\Api\V2;

use OrangeHRM\Framework\Http\Request as HttpRequest;

class Request
{
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_PATCH = 'PATCH';
    public const METHOD_DELETE = 'DELETE';

    /**
     * @var HttpRequest
     */
    protected HttpRequest $httpRequest;

    /**
     * Request body parameters ($_POST)
     * @var ParameterBag
     */
    protected ParameterBag $body;

    /**
     * Parameters from URL
     * @var ParameterBag
     */
    protected ParameterBag $attributes;

    /**
     * Query string parameters ($_GET)
     * @var ParameterBag
     */
    protected ParameterBag $query;

    public function __construct(HttpRequest $httpRequest)
    {
        $this->httpRequest = $httpRequest;
        $this->body = new ParameterBag($this->getHttpRequest()->request->all());
        $this->attributes = new ParameterBag($this->getHttpRequest()->attributes->all());
        $this->query = new ParameterBag($this->getHttpRequest()->query->all());
    }

    /**
     * @return HttpRequest
     */
    public function getHttpRequest(): HttpRequest
    {
        return $this->httpRequest;
    }

    /**
     * @return ParameterBag
     */
    public function getBody(): ParameterBag
    {
        return $this->body;
    }

    /**
     * @param ParameterBag $body
     * @internal
     */
    public function setBody(ParameterBag $body): void
    {
        $this->body = $body;
    }

    /**
     * @return ParameterBag
     */
    public function getAttributes(): ParameterBag
    {
        return $this->attributes;
    }

    /**
     * @param ParameterBag $attributes
     * @internal
     */
    public function setAttributes(ParameterBag $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * @return ParameterBag
     */
    public function getQuery(): ParameterBag
    {
        return $this->query;
    }

    /**
     * @param ParameterBag $query
     * @internal
     */
    public function setQuery(ParameterBag $query): void
    {
        $this->query = $query;
    }

    /**
     * @return array
     */
    public function getAllParameters(): array
    {
        return array_merge(
            $this->getBody()->all(),
            $this->getAttributes()->all(),
            $this->getQuery()->all(),
        );
    }
}

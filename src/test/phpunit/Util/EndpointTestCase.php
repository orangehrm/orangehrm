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

namespace OrangeHRM\Tests\Util;

use Closure;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Api\V2\Exception\InvalidParamException;
use OrangeHRM\Core\Api\V2\Exception\NotImplementedException;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\Request;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Traits\ValidatorTrait;
use OrangeHRM\Framework\Http\Request as HttpRequest;
use PHPUnit\Framework\MockObject\MockBuilder;

abstract class EndpointTestCase extends KernelTestCase
{
    use ValidatorTrait;

    /**
     * @param array $query
     * @param array $request
     * @param array $attributes
     * @return Request
     */
    protected function getRequest(array $query = [], array $request = [], array $attributes = []): Request
    {
        $httpRequest = new HttpRequest($query, $request, $attributes);
        return new Request($httpRequest);
    }

    /**
     * @psalm-template T of object
     * @psalm-param class-string<T> $apiClassName
     * @psalm-return MockBuilder<T>
     */
    protected function getApiEndpointMockBuilder(string $apiClassName, array $requestParams = []): MockBuilder
    {
        $request = $this->getRequest(
            $requestParams[RequestParams::PARAM_TYPE_QUERY] ?? [],
            $requestParams[RequestParams::PARAM_TYPE_BODY] ?? [],
            $requestParams[RequestParams::PARAM_TYPE_ATTRIBUTE] ?? [],
        );

        return $this->getMockBuilder($apiClassName)->setConstructorArgs([$request]);
    }

    protected function expectNotImplementedException(): void
    {
        $this->expectException(NotImplementedException::class);
    }

    protected function expectRecordNotFoundException(): void
    {
        $this->expectException(RecordNotFoundException::class);
    }

    protected function expectBadRequestException(): void
    {
        $this->expectException(BadRequestException::class);
    }

    protected function expectInvalidParamException(): void
    {
        $this->expectException(InvalidParamException::class);
    }

    protected function expectForbiddenException(): void
    {
        $this->expectException(ForbiddenException::class);
    }

    /**
     * @param Closure $closure
     * @param array|null $invalidOnly
     */
    protected function assertInvalidParamException(Closure $closure, array $invalidOnly = []): void
    {
        try {
            $closure();
            $this->fail('Given validation closure not throwing ' . InvalidParamException::class);
        } catch (InvalidParamException $e) {
            sort($invalidOnly);
            $invalidParamKeys = array_keys($e->getErrorBag());
            sort($invalidParamKeys);
            $this->assertEquals(
                $invalidOnly,
                $invalidParamKeys,
                'Expected invalid parameters are: ' . implode(',', $invalidOnly) .
                '; But actual: ' . implode(',', $invalidParamKeys)
            );
        }
    }
}

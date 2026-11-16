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

namespace OrangeHRM\Tests\Util\Integration;

use OrangeHRM\Core\Authorization\Manager\UserRoleManagerFactory;
use OrangeHRM\Core\Controller\Rest\V2\GenericRestController;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Framework\Services;

/**
 * Sends a request to an endpoint through GenericRestController::handle(), the path production requests take:
 * route, query and body are kept apart, then the controller checks them, validates and calls the handler.
 * For use in an EndpointIntegrationTestCase.
 */
trait RestControllerTestTrait
{
    /**
     * @param int $userId logged-in user
     * @param string $apiClassName endpoint class, as `_api` in routes.yaml
     * @param string $method HTTP method
     * @param array $attributes route attributes, without `_api` and `_key`
     * @param array $query
     * @param array $body
     * @param array $services service id => class, as in TestCaseParams::setServices()
     * @param string|null $key `_key` from routes.yaml, if the route defines one
     * @return array [status code, decoded content]
     */
    protected function handleRestRequest(
        int $userId,
        string $apiClassName,
        string $method,
        array $attributes,
        array $query = [],
        array $body = [],
        array $services = [],
        ?string $key = null
    ): array {
        $testCaseParams = new TestCaseParams();
        $testCaseParams->setUserId($userId);
        $testCaseParams->setServices($services);
        $testCaseParams->setFactories([
            Services::USER_ROLE_MANAGER => [UserRoleManagerFactory::class, 'getNewUserRoleManager'],
        ]);
        $this->createKernelWithMockServices([
            Services::AUTH_USER => $this->getMockAuthUser($testCaseParams),
            // Handlers that save a record stamp it with the current time
            Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
        ]);
        $this->registerServices($testCaseParams);

        $attributes['_api'] = $apiClassName;
        if ($key !== null) {
            $attributes['_key'] = $key;
        }
        $httpRequest = $this->getHttpRequest($query, $body, $attributes);
        $httpRequest->setMethod($method);
        $response = (new GenericRestController())->handle($httpRequest);
        // Later reads in the test come from the database, not from entities loaded by the request
        $this->getEntityManager()->clear();
        return [$response->getStatusCode(), json_decode($response->getContent(), true)];
    }

    /**
     * @param array $response [status code, decoded content] from handleRestRequest()
     * @param string $key the parameter sent with different values
     */
    protected function assertConflictRejected(array $response, string $key): void
    {
        [$status, $content] = $response;
        $this->assertSame(422, $status, json_encode($content));
        $this->assertSame([$key], $content['error']['data']['invalidParamKeys']);
        $this->assertArrayNotHasKey('data', $content);
    }

    /**
     * @param array $response [status code, decoded content] from handleRestRequest()
     * @return array the response data
     */
    protected function assertHandled(array $response): array
    {
        [$status, $content] = $response;
        $this->assertSame(200, $status, json_encode($content));
        return $content['data'];
    }
}

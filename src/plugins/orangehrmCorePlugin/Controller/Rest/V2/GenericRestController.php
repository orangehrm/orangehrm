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

namespace OrangeHRM\Core\Controller\Rest\V2;

use Exception;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\Exception\NotImplementedException;
use OrangeHRM\Core\Api\V2\Request;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Response;
use OrangeHRM\Core\Api\V2\Serializer\AbstractEndpointResult;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;

class GenericRestController extends AbstractRestController
{
    /**
     * @var null|Endpoint
     */
    protected ?Endpoint $apiEndpoint = null;

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function init(Request $request)
    {
        $apiEndpoint = $request->getAttributes()->get('_api');
        if (is_null($apiEndpoint)) {
            throw new Exception(
                sprintf(
                    'Please define `_api` attribute under `defaults` in `%s` within particular routes.yaml',
                    $request->getHttpRequest()->getPathInfo()
                )
            );
        }
        if (!class_exists($apiEndpoint)) {
            throw new Exception(
                sprintf('Could not found class `%s`. Hint: use fully qualified class name', $apiEndpoint)
            );
        }

        $this->apiEndpoint = new $apiEndpoint($request);

        if (!$this->apiEndpoint instanceof ResourceEndpoint && !$this->apiEndpoint instanceof CollectionEndpoint) {
            throw $this->getNotInstanceOfException(
                ResourceEndpoint::class . '` or `' . CollectionEndpoint::class
            );
        }
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function handleGetRequest(Request $request): Response
    {
        if (!$this->isGetOneRequest($request)) {
            if ($this->apiEndpoint instanceof CollectionEndpoint) {
                $result = $this->apiEndpoint->getAll();
            } else {
                throw $this->getNotInstanceOfException(CollectionEndpoint::class);
            }
        } else {
            if ($this->apiEndpoint instanceof ResourceEndpoint) {
                $result = $this->apiEndpoint->getOne();
            } else {
                throw $this->getNotInstanceOfException(ResourceEndpoint::class);
            }
        }
        return new Response(...$this->getPreparedResponseParamsFromResult($result));
    }

    /**
     * @inheritDoc
     */
    protected function initGetValidationRule(Request $request): ?ParamRuleCollection
    {
        $this->getValidationRule = $this->isGetOneRequest($request) ?
            ($this->apiEndpoint instanceof ResourceEndpoint ?
                $this->apiEndpoint->getValidationRuleForGetOne() :
                null) :
            ($this->apiEndpoint instanceof CollectionEndpoint ?
                $this->apiEndpoint->getValidationRuleForGetAll() :
                null);
        return $this->getValidationRule;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function handlePostRequest(Request $request): Response
    {
        if ($this->apiEndpoint instanceof CollectionEndpoint) {
            $result = $this->apiEndpoint->create();
            return new Response(...$this->getPreparedResponseParamsFromResult($result));
        } else {
            throw $this->getNotInstanceOfException(CollectionEndpoint::class);
        }
    }

    /**
     * @inheritDoc
     */
    protected function initPostValidationRule(Request $request): ?ParamRuleCollection
    {
        $this->postValidationRule = $this->apiEndpoint instanceof CollectionEndpoint ?
            $this->apiEndpoint->getValidationRuleForCreate() : null;
        return $this->postValidationRule;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function handlePutRequest(Request $request): Response
    {
        if ($this->apiEndpoint instanceof ResourceEndpoint) {
            $result = $this->apiEndpoint->update();
            return new Response(...$this->getPreparedResponseParamsFromResult($result));
        } else {
            throw $this->getNotInstanceOfException(ResourceEndpoint::class);
        }
    }

    /**
     * @inheritDoc
     */
    protected function initPutValidationRule(Request $request): ?ParamRuleCollection
    {
        $this->putValidationRule = $this->apiEndpoint instanceof ResourceEndpoint ?
            $this->apiEndpoint->getValidationRuleForUpdate() : null;
        return $this->putValidationRule;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function handleDeleteRequest(Request $request): Response
    {
        if ($this->apiEndpoint instanceof CollectionEndpoint || $this->apiEndpoint instanceof ResourceEndpoint) {
            $result = $this->apiEndpoint->delete();
            return new Response(...$this->getPreparedResponseParamsFromResult($result));
        } else {
            throw $this->getNotInstanceOfException(ResourceEndpoint::class . '` or `' . CollectionEndpoint::class);
        }
    }

    /**
     * @inheritDoc
     */
    protected function initDeleteValidationRule(Request $request): ?ParamRuleCollection
    {
        $this->deleteValidationRule = $this->apiEndpoint instanceof CollectionEndpoint ?
            $this->apiEndpoint->getValidationRuleForDelete() : null;
        return $this->deleteValidationRule;
    }

    /**
     * @param string $class
     * @return Exception
     */
    private function getNotInstanceOfException(string $class): Exception
    {
        return new NotImplementedException(
            sprintf('`%s` Endpoint is not an instance of `%s`', get_class($this->apiEndpoint), $class)
        );
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function isGetOneRequest(Request $request): bool
    {
        $idAttribute = $request->getAttributes()->get('_key', 'id');
        return $request->getAttributes()->has($idAttribute) || $request->getQuery()->has($idAttribute);
    }

    /**
     * @param AbstractEndpointResult $result
     * @return array
     */
    private function getPreparedResponseParamsFromResult(AbstractEndpointResult $result): array
    {
        $meta = $result->getMeta();
        $rels = $result->getRels();
        return [
            $result->normalize(),
            is_null($meta) ? [] : $meta->all(),
            is_null($rels) ? [] : $rels->all()
        ];
    }
}

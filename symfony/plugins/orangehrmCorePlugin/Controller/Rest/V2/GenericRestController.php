<?php

namespace OrangeHRM\Core\Controller\Rest\V2;

use Exception;
use OrangeHRM\Core\Api\V2\CollectionEndpointInterface;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\Request;
use OrangeHRM\Core\Api\V2\ResourceEndpointInterface;
use OrangeHRM\Core\Api\V2\Response;


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

        if (!$this->apiEndpoint instanceof ResourceEndpointInterface && !$this->apiEndpoint instanceof CollectionEndpointInterface) {
            throw $this->getNotInstanceOfException(
                ResourceEndpointInterface::class . '` or `' . CollectionEndpointInterface::class
            );
        }
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function handleGetRequest(Request $request): Response
    {
        $idAttribute = $request->getAttributes()->get('_key', 'id');
        if (is_null($request->getAttributes()->get($idAttribute))) {
            if ($this->apiEndpoint instanceof CollectionEndpointInterface) {
                $result = $this->apiEndpoint->getAll();
            } else {
                throw $this->getNotInstanceOfException(CollectionEndpointInterface::class);
            }
        } else {
            if ($this->apiEndpoint instanceof ResourceEndpointInterface) {
                $result = $this->apiEndpoint->getOne();
            } else {
                throw $this->getNotInstanceOfException(ResourceEndpointInterface::class);
            }
        }
        $meta = $result->getMeta();
        $rels = $result->getRels();
        return new Response(
            $result->normalize(),
            is_null($meta) ? [] : $meta->all(),
            is_null($rels) ? [] : $rels->all()
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function handlePostRequest(Request $request): Response
    {
        if ($this->apiEndpoint instanceof CollectionEndpointInterface) {
            $result = $this->apiEndpoint->create();
            return new Response($result->normalize());
        } else {
            throw $this->getNotInstanceOfException(CollectionEndpointInterface::class);
        }
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function handlePutRequest(Request $request): Response
    {
        if ($this->apiEndpoint instanceof ResourceEndpointInterface) {
            $result = $this->apiEndpoint->update();
            return new Response($result->normalize());
        } else {
            throw $this->getNotInstanceOfException(ResourceEndpointInterface::class);
        }
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function handleDeleteRequest(Request $request): Response
    {
        if ($this->apiEndpoint instanceof CollectionEndpointInterface) {
            $result = $this->apiEndpoint->delete();
            return new Response($result->normalize());
        } else {
            throw $this->getNotInstanceOfException(CollectionEndpointInterface::class);
        }
    }

    /**
     * @param string $class
     * @return Exception
     */
    private function getNotInstanceOfException(string $class): Exception
    {
        return new Exception(
            sprintf('`%s` Endpoint is not an instance of `%s`', get_class($this->apiEndpoint), $class)
        );
    }
}

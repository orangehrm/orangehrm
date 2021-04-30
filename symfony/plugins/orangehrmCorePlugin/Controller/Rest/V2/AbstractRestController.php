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

namespace OrangeHRM\Core\Controller\Rest\V2;

use Exception;
use OrangeHRM\Core\Api\V2\Exception\NotImplementedException;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Controller\AbstractController;
use OrangeHRM\Framework\ServiceContainer;
use OrangeHRM\Framework\Services;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Validator;
use OrangeHRM\Core\Api\V2\Request;
use OrangeHRM\Core\Api\V2\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

abstract class AbstractRestController extends AbstractController
{
    protected array $getValidationRule = [];
    protected array $postValidationRule = [];
    protected array $putValidationRule = [];
    protected array $deleteValidationRule = [];

    /**
     * @param Request $request
     */
    protected function init(Request $request)
    {
    }

    /**
     * @param Request $request
     * @return Response
     */
    abstract protected function handleGetRequest(Request $request): Response;

    /**
     * @param Request $request
     * @return Response
     */
    abstract protected function handlePostRequest(Request $request): Response;

    /**
     * @param Request $request
     * @return Response
     */
    abstract protected function handlePutRequest(Request $request): Response;

    /**
     * @param Request $request
     * @return Response
     */
    abstract protected function handleDeleteRequest(Request $request): Response;

    /**
     * @param HttpRequest $request
     * @return array
     */
    protected function getValidationRule(HttpRequest $request): array
    {
        switch ($request->getMethod()) {
            case Request::METHOD_GET;
                return $this->getValidationRule;

            case Request::METHOD_POST:
                return $this->postValidationRule;

            case Request::METHOD_PUT:
                return $this->putValidationRule;

            case Request::METHOD_DELETE:
                return $this->deleteValidationRule;

            default:
                return [];
        }
    }

    /**
     * @param HttpRequest $httpRequest
     * @return string
     */
    public function handle(HttpRequest $httpRequest)
    {
        $request = new Request($httpRequest);
        $this->init($request);
        $response = new HttpResponse();
        $response->headers->set('Content-type', 'application/json');
        try {
            if (!empty($this->getValidationRule($httpRequest))) {
                Validator::validate($request->getAllParameters(), $this->getValidationRule($httpRequest));
            }
            switch ($httpRequest->getMethod()) {
                case Request::METHOD_GET;
                    $response->setContent($this->handleGetRequest($request)->formatData());
                    break;

                case Request::METHOD_POST:
                    $response->setContent($this->handlePostRequest($request)->format());
                    break;

                case Request::METHOD_PUT:
                    $response->setContent($this->handlePutRequest($request)->format());
                    break;

                case Request::METHOD_DELETE:
                    $response->setContent($this->handleDeleteRequest($request)->format());
                    break;

                default:
                    throw new NotImplementedException();
            }
        } catch (RecordNotFoundException $e) {
            $response->setContent(
                Response::formatError(
                    ['error' => ['status' => '404', 'message' => $e->getMessage()]]
                )
            );
            $response->setStatusCode(404);
        } catch (InvalidParamException $e) {
            $response->setContent(
                Response::formatError(
                    ['error' => ['status' => '202', 'message' => $e->getMessage()]]
                )
            );
            $response->setStatusCode(202);
        } catch (NotImplementedException $e) {
            $response->setContent(
                Response::formatError(
                    ['error' => ['status' => '501', 'message' => 'Not Implemented']]
                )
            );
            $response->setStatusCode(501);
        } catch (BadRequestException $e) {
            $response->setContent(
                Response::formatError(
                    ['error' => ['status' => '400', 'message' => 'Bad Request']]
                )
            );
            $response->setStatusCode(400);
        } catch (Exception $e) {
            /** @var LoggerInterface $logger */
            $logger = ServiceContainer::getContainer()->get(Services::LOGGER);
            $logger->error($e->getMessage());
            $logger->error($e->getTraceAsString());

            $response->setContent(
                Response::formatError(
                    ['error' => ['status' => '500', 'message' => 'Unexpected Error Occurred']]
                )
            );
            $response->setStatusCode(500);
        }

        return $response;
    }
}

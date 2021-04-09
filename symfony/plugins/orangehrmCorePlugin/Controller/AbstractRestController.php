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

namespace OrangeHRM\Core\Controller;

use Exception;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\NotImplementedException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Validator;
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

abstract class AbstractRestController extends AbstractController
{
    protected array $getValidationRule = [];
    protected array $postValidationRule = [];
    protected array $putValidationRule = [];
    protected array $deleteValidationRule = [];

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
     * @throws NotImplementedException
     */
    protected function handlePutRequest(Request $request): Response
    {
        throw new NotImplementedException();
    }

    /**
     * @param Request $request
     * @return Response
     * @throws NotImplementedException
     */
    protected function handleDeleteRequest(Request $request): Response
    {
        throw new NotImplementedException();
    }

    /**
     * @param HttpRequest $request
     * @return array
     */
    protected function getValidationRule(HttpRequest $request): array
    {
        switch ($request->getMethod()) {
            case 'GET';
                return $this->getValidationRule;

            case 'POST':
                return $this->postValidationRule;

            case 'PUT':
                return $this->putValidationRule;

            case 'DELETE':
                return $this->deleteValidationRule;

            default:
                return [];
        }
    }

    /**
     * @param HttpRequest $request
     * @return string
     */
    public function handle(HttpRequest $request)
    {
        $httpRequest = new Request($request);
        $this->init($httpRequest);
        $response = new HttpResponse();
        $response->headers->set('Content-type', 'application/json');
        try {
            if (!empty($this->getValidationRule($request))) {
                Validator::validate($httpRequest->getAllParameters(), $this->getValidationRule($request));
            }
            switch ($request->getMethod()) {
                case 'GET';
                    $response->setContent($this->handleGetRequest($httpRequest)->formatData());
                    break;

                case 'POST':
                    $response->setContent($this->handlePostRequest($httpRequest)->format());
                    break;

                case 'PUT':
                    $response->setContent($this->handlePutRequest($httpRequest)->format());
                    break;

                case 'DELETE':
                    $response->setContent($this->handleDeleteRequest($httpRequest)->format());
                    break;

                default:
                    throw new NotImplementedException();
            }
        } catch (RecordNotFoundException $e) {
            $response->setContent(
                Response::formatError(
                    ['error' => ['status' => '404', 'text' => $e->getMessage()]]
                )
            );
            $response->setStatusCode(404);
        } catch (InvalidParamException $e) {
            $response->setContent(
                Response::formatError(
                    ['error' => ['status' => '202', 'text' => $e->getMessage()]]
                )
            );
            $response->setStatusCode(202);
        } catch (NotImplementedException $e) {
            $response->setContent(
                Response::formatError(
                    ['error' => ['status' => '501', 'text' => 'Not Implemented']]
                )
            );
            $response->setStatusCode(501);
        } catch (BadRequestException $e) {
            $response->setContent(
                Response::formatError(
                    ['error' => [$e->getMessage()]]
                )
            );
            $response->setStatusCode(400);
        } catch (Exception $e) {
            $response->setContent(
                Response::formatError(
                    ['error' => ['Unexpected Error Occurred']]
                )
            );
            $response->setStatusCode(500);
        }

        return $response;
    }
}

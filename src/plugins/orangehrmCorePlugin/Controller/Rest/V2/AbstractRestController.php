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

use Error;
use Exception;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Api\V2\Exception\InvalidParamException;
use OrangeHRM\Core\Api\V2\Exception\NotImplementedException;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\Request;
use OrangeHRM\Core\Api\V2\Response;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Validator;
use OrangeHRM\Core\Controller\AbstractController;
use OrangeHRM\Core\Traits\LoggerTrait;
use OrangeHRM\Framework\Http\Request as HttpRequest;
use OrangeHRM\Framework\Http\Response as HttpResponse;
use OrangeHRM\I18N\Traits\Service\I18NHelperTrait;

abstract class AbstractRestController extends AbstractController
{
    use LoggerTrait;
    use I18NHelperTrait;

    protected ?ParamRuleCollection $getValidationRule = null;
    protected ?ParamRuleCollection $postValidationRule = null;
    protected ?ParamRuleCollection $putValidationRule = null;
    protected ?ParamRuleCollection $deleteValidationRule = null;

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
     * @param Request $request
     * @return ParamRuleCollection|null
     */
    abstract protected function initGetValidationRule(Request $request): ?ParamRuleCollection;

    /**
     * @param Request $request
     * @return ParamRuleCollection|null
     */
    abstract protected function initPostValidationRule(Request $request): ?ParamRuleCollection;

    /**
     * @param Request $request
     * @return ParamRuleCollection|null
     */
    abstract protected function initPutValidationRule(Request $request): ?ParamRuleCollection;

    /**
     * @param Request $request
     * @return ParamRuleCollection|null
     */
    abstract protected function initDeleteValidationRule(Request $request): ?ParamRuleCollection;

    /**
     * @param Request $request
     * @return ParamRuleCollection|null
     */
    protected function getValidationRule(Request $request): ?ParamRuleCollection
    {
        switch ($request->getHttpRequest()->getMethod()) {
            case Request::METHOD_GET:
                return $this->initGetValidationRule($request);

            case Request::METHOD_POST:
                return $this->initPostValidationRule($request);

            case Request::METHOD_PUT:
                return $this->initPutValidationRule($request);

            case Request::METHOD_DELETE:
                return $this->initDeleteValidationRule($request);

            default:
                return null;
        }
    }

    /**
     * @param HttpRequest $httpRequest
     * @return HttpResponse
     * @throws Exception
     */
    public function handle(HttpRequest $httpRequest): HttpResponse
    {
        $request = new Request($httpRequest);
        $this->init($request);
        $response = new HttpResponse();
        $response->headers->set(Response::CONTENT_TYPE_KEY, Response::CONTENT_TYPE_JSON);
        try {
            $validationRule = $this->getValidationRule($request);
            if ($validationRule instanceof ParamRuleCollection) {
                Validator::validate($request->getAllParameters(), $validationRule);
            }
            switch ($httpRequest->getMethod()) {
                case Request::METHOD_GET:
                    $response->setContent($this->handleGetRequest($request)->formatData());
                    break;

                case Request::METHOD_POST:
                    $response->setContent($this->handlePostRequest($request)->formatData());
                    break;

                case Request::METHOD_PUT:
                    $response->setContent($this->handlePutRequest($request)->formatData());
                    break;

                case Request::METHOD_DELETE:
                    $response->setContent($this->handleDeleteRequest($request)->formatData());
                    break;

                default:
                    throw new NotImplementedException();
            }
        } catch (RecordNotFoundException $e) {
            $this->getLogger()->info($e->getMessage());
            $this->getLogger()->info($e->getTraceAsString());

            $response->setContent(
                Response::formatError(
                    ['error' => ['status' => '404', 'message' => $e->getMessage()]]
                )
            );
            $response->setStatusCode(404);
        } catch (InvalidParamException $e) {
            $this->getLogger()->info($e->getMessage());
            $this->getLogger()->info($e->getTraceAsString());

            // TODO:: should format to show multiple errors
            $response->setContent(
                Response::formatError(
                    [
                        'error' => [
                            'status' => '422',
                            'message' => $this->getI18NHelper()->transBySource($e->getMessage()),
                            'data' => $e->getNormalizedErrorBag()
                        ]
                    ]
                )
            );
            $response->setStatusCode(422);
        } catch (NotImplementedException $e) {
            $this->getLogger()->info($e->getMessage());
            $this->getLogger()->info($e->getTraceAsString());

            $response->setContent(
                Response::formatError(
                    ['error' => ['status' => '501', 'message' => 'Not Implemented']]
                )
            );
            $response->setStatusCode(501);
        } catch (BadRequestException $e) {
            $this->getLogger()->info($e->getMessage());
            $this->getLogger()->info($e->getTraceAsString());

            $response->setContent(
                Response::formatError(
                    [
                        'error' => [
                            'status' => '400',
                            'message' => $this->getI18NHelper()->transBySource($e->getMessage())
                        ]
                    ]
                )
            );
            $response->setStatusCode(400);
        } catch (ForbiddenException $e) {
            // Escape this exception to handle it in
            // \OrangeHRM\Core\Subscriber\ApiAuthorizationSubscriber::onExceptionEvent
            throw $e;
        } catch (Exception $e) {
            $this->getLogger()->error($e->getMessage());
            $this->getLogger()->error($e->getTraceAsString());

            $response->setContent(
                Response::formatError(
                    ['error' => ['status' => '500', 'message' => 'Unexpected Error Occurred']]
                )
            );
            $response->setStatusCode(500);
        } catch (Error $e) {
            $this->getLogger()->critical($e->getMessage());
            $this->getLogger()->critical($e->getTraceAsString());

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

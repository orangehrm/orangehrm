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
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\NotImplementedException;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Validator;

abstract class baseRestAction extends baseOAuthAction {

    protected $getValidationRule = array();
    protected $postValidationRule = array();
    protected $putValidationRule = array();
    protected $deleteValidationRule = array();

    /**
     * Check token validation
     */
    public function preExecute() {


        parent::preExecute();

        $server = $this->getOAuthServer();
        $oauthRequest = $this->getOAuthRequest();
        $oauthResponse = $this->getOAuthResponse();
        if (!$server->verifyResourceRequest($oauthRequest, $oauthResponse)) {
            $server->getResponse()->send();
            exit;
        }
    }

    protected function init(Request $request){

    }

    /**
     * @param Request $request
     * @return Response
     */
    abstract protected function handleGetRequest(Request $request);

    /**
     * @param Request $request
     * @return Response
     */
    abstract protected function handlePostRequest(Request $request);

    /**
     * @param Request $request
     * @throws NotImplementedException
     */
    protected function handlePutRequest(Request $request){
        throw new NotImplementedException('method not implemented');
    }

    /**
     * @param Request $request
     * @throws NotImplementedException
     */
    protected function handleDeleteRequest(Request $request){
        throw new NotImplementedException('method not implemented');
    }

    /**
     * @return array
     */
    protected function getValidationRule($request) {
        switch($request->getMethod()){
            case 'GET';
                return $this->getValidationRule;
                break;
            case 'POST':
                return $this->postValidationRule;
                break;
            case 'PUT':
                return $this->putValidationRule;
                break;
            case 'DELETE':
                return $this->deleteValidationRule;
                break;
        }
    }

    /**
     * @param sfRequest $request
     * @return string
     */
    public function execute($request) {

        $httpRequest = new Request($request);
        $this->init($httpRequest);
        $response = $this->getResponse();
        $response->setHttpHeader('Content-type', 'application/json');
        try{

            if(!empty($this->getValidationRule($request))) {
                Validator::validate($httpRequest->getAllParameters(),$this->getValidationRule($request));
            }
            switch($request->getMethod()){
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
            }

        } catch (RecordNotFoundException $e){
            $response->setContent(Response::formatError(
                array('error'=>array('status'=>'404','text'=>$e->getMessage())))
            );
            $response->setStatusCode(404);
        } catch (InvalidParamException $e){
            $response->setContent(Response::formatError(
                array('error'=>array('status'=>'202','text'=>$e->getMessage())))
            );
            $response->setStatusCode(202);
        } catch (NotImplementedException $e){
            $response->setContent(Response::formatError(
                array('error'=>array('status'=>'501','text'=>'Not Implemented')))
            );
            $response->setStatusCode(501);
        } catch(BadRequestException $e) {
            $response->setContent(Response::formatError(
                array('error'=>array($e->getMessage())))
            );
            $response->setStatusCode(400);
        } catch(Exception $e) {
            $response->setContent(Response::formatError(
                array('error'=>array($e->getMessage())))
            );
            $response->setStatusCode(500);
        }


        return sfView::NONE;
    }
}


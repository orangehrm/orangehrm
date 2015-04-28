<?php

/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms and conditions on using this software.
 *
 */
class WSHelper extends baseWSUtility {

    const FORMAT_RAW = 'raw';
    const FORMAT_JSON = 'json';

    /**
     *
     * @param sfWebRequest $request 
     * @return WSRequestParameters
     */
    public function extractParameters(sfWebRequest $request) {
        $webRequestParameters = new WSRequestParameters();

        $logger = Logger::getLogger('core.webservices');

        $requestMethod = $request->getMethod();
        $contentType = $request->getContentType();
        $logger->debug("HTTP Method: $requestMethod, Content-Type: $contentType");
        
        $requestParameters = $request->getRequestParameters();
        
        $logger->debug("Request Parameters: " . print_r($requestParameters, true));
        if (!isset($requestParameters['ws_method'])) {
            throw new WebServiceException('Web service method is not specified', 400);
        }

        $webServiceMethod = $requestParameters['ws_method'];

        $methodParameters = array();

        // Checking for deprecated method of sending parameters using an http header
        $header = $request->getHttpHeader('ohrm_ws_method_parameters');

        if (!empty($header)) {
            $methodParameters = json_decode($header, true);
            if (!is_array($methodParameters)) {
                throw new WebServiceException("header ohrm_ws_method_parameters should be json encoded", 400);
            }

        } else {
            // get request parameters in URL (eg: /empNumber/11) after removing the default parameters
            $methodParameters = array_diff_key($requestParameters, array_flip(array('action', 'module', 'ws_method', '_sf_route')));

            // Merge with GET parameters
            $methodParameters = array_merge($methodParameters, $request->getGetParameters());

            // Check for JSON encoded body
            if ($contentType === 'application/json') {            
                $postParams = json_decode(file_get_contents('php://input'), true);                
                $methodParameters = array_merge($methodParameters, $postParams);
            } else if ($requestMethod === 'POST') {
                $methodParameters = array_merge($methodParameters, $request->getPostParameters());
            }

        }
       
//        $arrayName = $this->getArrayNameForFunction($function);
//        if(!array_key_exists($arrayName, $methodParameters)) {
//            throw new WebServiceException('Required array name not provided', 404);
//        }

     
        $webRequestParameters->setRequestMethod($requestMethod);
        $webRequestParameters->setMethod($webServiceMethod);
        $webRequestParameters->setParameters($methodParameters);
//        $webRequestParameters->setAppId($authenticationParamerters['app_id']);
//        $webRequestParameters->setAppToken($authenticationParamerters['app_token']);
//        $webRequestParameters->setSessionToken($authenticationParamerters['session_token']);

        return $webRequestParameters;
    }
    
    protected function getArrayNameForFunction($function)
    {
        switch ($function) {
            case 'updateEmployee':
                $paramName = 'employee';
                break;
            case 'bulkUpdateEmployees':
                $paramName = 'employees';
                break;
            case 'addEmployee':
                $paramName = 'employee';
                break;
            case 'addAppraisal':
                $paramName = 'appraisal';
                break;
            case 'updateUser':
                $paramName = 'user';
                break;
        }
        return $paramName;
    }

    /**
     *
     * @param mixed $result 
     * @return string
     */
    public function formatResult($result, $format = self::FORMAT_RAW) {
        if ($format == self::FORMAT_RAW) {
            return $result;
        }
        return $this->getWSUtilityService()->format($result, $format);
    }

}

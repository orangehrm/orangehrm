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
    public function extractParamerts(sfWebRequest $request) {
        $webRequestParameters = new WSRequestParameters();

        $requestMethod = $request->getMethod();
        $parameters = array_keys($request->getRequestParameters());

        if (count($parameters) < 4) {
            throw new WebServiceException('Web service method is not specified', 1001);
        }

        //$authenticationParamerters = json_decode($request->getHttpHeader('ohrm_ws_auth_parameters'), true);
        $methodParameters = json_decode($request->getHttpHeader('ohrm_ws_method_parameters'), true);
        
//        if (!is_array($authenticationParamerters)) {
//            throw new WebServiceException('Authentication parameters are sent in a wrong format', 1002);
//        }
        
        if (!is_array($methodParameters)) {
            throw new WebServiceException('Method parameters are sent in a wrong format', 1003);
        }
        
        $webRequestParameters->setRequestMethod($requestMethod);
        $webRequestParameters->setMethod($parameters[2]);
        $webRequestParameters->setParameters($methodParameters);
//        $webRequestParameters->setAppId($authenticationParamerters['app_id']);
//        $webRequestParameters->setAppToken($authenticationParamerters['app_token']);
//        $webRequestParameters->setSessionToken($authenticationParamerters['session_token']);

        return $webRequestParameters;
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

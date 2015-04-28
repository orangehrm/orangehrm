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

class wsCallAction extends sfAction {
    
    private $logger = null;  
  
    protected function getWebServiceLogger() {  
        if (is_null($this->logger)) {  
            $this->logger = Logger::getLogger('core.webservices.log');  
        }  
  
        return($this->logger);  
    }

    public function execute($request) {
        $logger = $this->getWebServiceLogger();

        $wsHelper = new WSHelper();
        $wsManager = new WSManager();
        
        $result = '';
        $status = 'INITIAL';
        $contentType = 'text/plain';
        $httpStatus = 200;
        $httpStatusText = null;

        try {
            $paramObj = $wsHelper->extractParameters($request);
            $isMethodAvailable = $wsManager->isMethodAvailable($paramObj->getMethod(), $paramObj->getRequestMethod());           

            $logger->debug(print_r($paramObj, true));
            $logger->debug("MethodAvailable:" . $isMethodAvailable);
            
            if ($isMethodAvailable) {
                $isAuthenticated = $wsManager->isAuthenticated($paramObj);
                $isAuthorized = $wsManager->isAuthorized($paramObj);

                if ($isAuthenticated && $isAuthorized) {
                        
                        $result = $wsManager->callMethod($paramObj);
                        $logger->debug(print_r($result, true));
                        $result = $wsHelper->formatResult($result, WSHelper::FORMAT_JSON);
                        $logger->debug(print_r($result, true));
                        $status = 'SUCCESS';
                        $contentType = 'text/json';
                } else {
                    $result = 'NOT ALLOWED';
                    $status = 'ERROR';
                    $httpStatus = 401;
                }

            } else {
                $result = 'INVALID REQUEST';
                $status = 'ERROR';
                $httpStatus = 404;
                $httpStatusText = 'Webservice Method Not Found (' . $paramObj->getMethod() . ')';
            }
        } catch (WebServiceException $e) {
            $result = $e->getCode() . ': ' . $e->getMessage();
            $status = 'ERROR'; 
            $httpStatus = $e->getCode();
            $httpStatusText = $e->getMessage();
            
        } catch (Exception $e) {
            $logger = $this->getWebServiceLogger();
            $logger->info('Uncaught Exception: ' . $e->getMessage());
            $result = $e->getMessage().' '.$e->getCode();
            $status = 'ERROR';
        }

        $response = $this->getResponse();

        $response->setContent($result);
        $response->setHttpHeader('Content-type', $contentType);
        $response->setHttpHeader('ohrm_ws_call_status', $status);
        $response->setStatusCode($httpStatus, $httpStatusText);

        return sfView::NONE;
    }

}

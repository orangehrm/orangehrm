<?php

/*
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

/**
 * Description of getProviderJsonAction
 *
 * @author orangehrm
 */
class getProviderJsonAction extends baseOpenIdAction {
    
    private $authProviderExtraDetailsService;

    public function getAuthProviderExtraDetailsService() {
        if (is_null($this->authProviderExtraDetailsService)) {
            $this->authProviderExtraDetailsService = new AuthProviderExtraDetailsService();
        }
        return $this->authProviderExtraDetailsService;
    }

    public function execute($request) {
        $this->setLayout(false);
        sfConfig::set('sf_web_debug', false);
        sfConfig::set('sf_debug', false);

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
        }

        $providerId = $request->getParameter('id');
        $provider = $this->getOpenIdProviderService()->getOpenIdProvider($providerId);

        $authProviderExtraDetails = $this->getAuthProviderExtraDetailsService()->getAuthProviderDetailsByProviderId($providerId);
        if ($authProviderExtraDetails instanceof AuthProviderExtraDetails) {
            $result = array_merge($provider->toArray(), $authProviderExtraDetails->toArray());
            return $this->renderText(json_encode($result));
        }

        return $this->renderText(json_encode($provider->toArray()));
    }

}
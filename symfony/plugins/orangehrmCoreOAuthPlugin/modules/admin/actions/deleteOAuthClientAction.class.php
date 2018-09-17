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
 * Class deleteOAuthClientAction
 */
class deleteOAuthClientAction extends sfAction
{

    protected $oAuthService;

    /**
     * @return mixed
     */
    public function getOAuthService()
    {
        if($this->oAuthService == null){
            $this->oAuthService =  new OAuthService();
        }
        return $this->oAuthService;
    }

    /**
     * @param mixed $oAuthService
     */
    public function setOAuthService($oAuthService)
    {
        $this->oAuthService = $oAuthService;
    }

    public function execute($request)
    {

        $form = new DefaultListForm();
        $form->bind($request->getParameter($form->getName()));
        $toBeDeletedIds = $request->getParameter('chkSelectRow');

        if ($request->isMethod(sfWebRequest::POST)) {
            if ($form->isValid()) {

                $this->getOAuthService()->deleteOAuthClient($toBeDeletedIds);
                $this->getUser()->setFlash('success', __(TopLevelMessages::DELETE_SUCCESS));
                $this->redirect('admin/registerOAuthClient');
            } else {
                $this->getUser()->setFlash('warning', __(TopLevelMessages::FORM_VALIDATION_ERROR));
                $this->redirect($request->getReferer());
            }
        }
        return sfView::NONE;
    }
}

?>

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
 * Description of openIdProviderActionclass
 *
 * @author orangehrm
 */
class openIdProviderAction extends baseOpenIdAction {

    public function execute($request) {
        //$request->setParameter('initialActionName', 'authenticationConfiguration');
        $usrObj = $this->getUser()->getAttribute('user');
        if (!$usrObj->isAdmin()) {
            $this->redirect('pim/viewPersonalDetails');
        }
        $this->setForm($this->getOpenIdProviderForm());
        $openIdProviderList = $this->getOpenIdProviderService()->listOpenIdProviders();
        if (count($openIdProviderList) > 0) {
            $this->getConfigService()->setOpenIdProviderAdded(self::IS_PROVIDER_ADDED_ON);
        } else {
            $this->getConfigService()->setOpenIdProviderAdded(self::IS_PROVIDER_ADDED_OFF);
        }
        $this->setListComponent($openIdProviderList);

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $flag = $this->saveOpenIdProvider($this->form);
                if ($flag == 'save') {
                    $this->getUser()->setFlash('success', __(TopLevelMessages::SAVE_SUCCESS));
                } else {
                    $this->getUser()->setFlash('success', __(TopLevelMessages::UPDATE_SUCCESS));
                }
                $this->redirect('admin/openIdProvider');
            } else {
                $this->getUser()->setFlash('warning', __(TopLevelMessages::FORM_VALIDATION_ERROR));
                $this->redirect($request->getReferer());
            }
        }
        
    }
    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }
    private function setListComponent($openIdProviderList) {

        $configurationFactory = new OpenIdProviderHeaderListConfigurationFactory();

        $runtimeDefinitions = $this->setRuntimeDefinitions();
        $configurationFactory->setRuntimeDefinitions($runtimeDefinitions);

        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($openIdProviderList);
    }

    private function setRuntimeDefinitions(){
        $runtimeDefinitions = array();
        $buttons = array();

        $buttons['Add'] = array('label' => 'Add');
        $runtimeDefinitions['hasSelectableRows'] = true;
        $runtimeDefinitions['idValueGetter'] = 'getProviderId';
        $buttons['Delete'] = array('label' => 'Delete',
            'type' => 'submit',
            'data-toggle' => 'modal',
            'data-target' => '#deleteConfModal',
            'class' => 'delete');

        $runtimeDefinitions['buttons'] = $buttons;
        $runtimeDefinitions['buttonsPosition'] = 'before-data';
        $runtimeDefinitions['title'] = 'Provider List';

        $runtimeDefinitions['formMethod']='post';
        $runtimeDefinitions['formAction'] ='admin/deleteProviders';
        $runtimeDefinitions['hasSummary']= false;
        return $runtimeDefinitions;
    }

    public function getOpenIdProviderForm() {
        $form = new AuthProviderExtraDetailsForm();
        return $form;
    }

    public function saveOpenIdProvider($form) {
        $flag = $form->save();
        return $flag;
    }

}

?>

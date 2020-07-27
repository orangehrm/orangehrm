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

/**
 * Description of registerOAuthClientAction
 *
 * @author orangehrm
 */
class registerOAuthClientAction extends ohrmBaseAction {

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

    /**
     * return true if the logged in user is authorized to add new oauth clients flase otherwise
     * @return boolean 
     */
    protected function isAuthorizedUser(){
        $sfUser = sfContext::getInstance()->getUser();
        $userId = $sfUser->getAttribute('auth.userId');
        if(empty($userId)){
            //show login page
            $this->forward('auth', 'login');
        } else {
            if ($sfUser->getAttribute('auth.isAdmin') == "Yes") {                
                return true;
            }
        }
        
        return false;
    }
    
    public function preExecute() {
        $this->authorized = $this->isAuthorizedUser();
    }
    
    public function execute($request) {  
        $form = new OAuthClientRegistrationForm();

        if($request->isMethod(sfWebRequest::POST)){ 
            $form->bind($request->getPostParameter($form->getName()));
            if($form->isValid()){

                // code to handle form submission
                $values = $form->getValues();
                $client = $this->getOAuthService()->getOAuthClient($values['client_id']);

                if($client instanceof OAuthClient ){

                    $client->setClientSecret($values['client_secret']);
                    $client->setRedirectUri($values['redirect_uri']);


                } else {

                    $client = new OAuthClient();
                    $client->setClientId($values['client_id']);
                    $client->setClientSecret($values['client_secret']);
                    $client->setRedirectUri($values['redirect_uri']);
                    $client->setGrantTypes(GrantType::CLIENT_CREDENTIALS);
                    $client->setScope(Scope::SCOPE_ADMIN);
                }

                try{
                    $client->save();  
                    $this->getUser()->setFlash("success", __("OAuth Client Saved Successfully"), false);
                }  catch (Exception $e){
                    if($e->getCode() == 23000){ // ER_DUP_ENTRY : duplicate client_id. client may already registered 
                        $this->getUser()->setFlash("warning", __("Given Client ID Is Already In The Database"), false);
                    } else {
                        die($e->getMessage());
                    }
                }
                
            } else {
                $this->handleBadRequest();
                $this->getUser()->setFlash('warning', __(TopLevelMessages::VALIDATION_FAILED), false);
            }
        }
        $oauthClients = $this->getOAuthService()->listOAuthClients();
        $this->setListComponent($oauthClients);

        if($this->authorized){
            $this->form = $form;
        }  
    }   
    
    private function isAlreadyRegisrtered($client_id){
//        $q = Doctrine_Query::create();
//        $q->from('OAuthClient')->fetchOne();
        return false;
    }


    private function setListComponent($clientList) {

        $configurationFactory = new OAuthClientHeaderListConfigurationFactory();
        $runtimeDefinitions = $this->setRuntimeDefinitions();
        $configurationFactory->setRuntimeDefinitions($runtimeDefinitions);
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($clientList);
    }

    private function setRuntimeDefinitions(){
        $runtimeDefinitions = array();
        $buttons = array();

        $runtimeDefinitions['hasSelectableRows'] = true;
        $runtimeDefinitions['unselectableRowIds'] = [OAuthClientDao::PUBLIC_MOBILE_CLIENT_ID];
        $runtimeDefinitions['idValueGetter'] = 'getClientId';
        $buttons['Delete'] = array('label' => 'Delete',
            'type' => 'submit',
            'data-toggle' => 'modal',
            'data-target' => '#deleteConfModal',
            'class' => 'delete');

        $runtimeDefinitions['buttons'] = $buttons;
        $runtimeDefinitions['buttonsPosition'] = 'before-data';
        $runtimeDefinitions['title'] = 'OAuth Client List';

        $runtimeDefinitions['formMethod']='post';
        $runtimeDefinitions['formAction'] ='admin/deleteOAuthClient';
        $runtimeDefinitions['hasSummary']= false;
        return $runtimeDefinitions;
    }

}

?>

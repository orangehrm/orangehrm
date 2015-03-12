<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of registerOAuthClientAction
 *
 * @author orangehrm
 */
class registerOAuthClientAction extends sfAction {
    
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
                $client = new OAuthClient();
                $client->setClientId($values['client_id']);
                $client->setClientSecret($values['client_secret']);
                $client->setRedirectUri($values['redirect_uri']);
                try{
                    $client->save();  
                    $this->getUser()->setFlash("success", __("OAuth Client Saved Successfully"), false);
                }  catch (Exception $e){
                    if($e->getCode() == 23000){ // ER_DUP_ENTRY : duplicate client_id. client may already registered 
                        $this->getUser()->setFlash("warning", __("given Client ID is already in the database"), false);
                    } else {
                        die($e->getMessage());
                    }
                }
                
            }
        }
        if($this->authorized){
            $this->form = $form;
        }  
    }   
    
    private function isAlreadyRegisrtered($client_id){
//        $q = Doctrine_Query::create();
//        $q->from('OAuthClient')->fetchOne();
        return false;
    }
}

?>

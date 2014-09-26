<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of beaconRegistrationAction
 *
 * @author chathura
 */
class beaconRegistrationAction extends sfAction{
    
   
    
    public function setForm($form) {
        if(is_null($this->form)) {
            $this->form = $form;
        }        
    }
    
    /**
     * 
     * @param sfRequest $request
     */
    public function execute($request) {
        
        $this->setForm(new BeaconRegistrationForm());
        if($request->isMethod('post')) {
            $this->form->bind($request->getParameter($this->form->getName()));
             if ($this->form->isValid()) {
                 $result = $this->form->save();
                 $this->getUser()->setFlash($result['messageType'], $result['message']);
             }
        }
    }

}

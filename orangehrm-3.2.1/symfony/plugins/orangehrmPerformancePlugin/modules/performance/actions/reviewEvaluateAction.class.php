<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of reviewEvaluateAction
 *
 * @author nadeera
 */
class reviewEvaluateAction extends basePeformanceAction {  

    public $reviewEvaluationForm;
    
    protected function _checkAuthentication($request = null) {
        
        $options ['id'] = $request->getParameter('id');                
        $form = $this->getReviewEvaluationForm($options);
        
        foreach ($form->getReviewers() as $reviewer){
            if($this->getUser()->getAttribute('user')->getEmployeeNumber() == $reviewer->getEmployeeNumber()){ 
                return true;
            }
        }
        $this->redirect('pim/viewPersonalDetails');       
    }
    
    /**
     *
     * @return /ReviewEvaluationForm 
     */
    public function getReviewEvaluationForm($options = array()) {
        
        if($this->reviewEvaluationForm == null ){
            $form =  new ReviewEvaluationForm(array(), $options);
            $form->setUser($this->getUser());
            return $form;
        } else {
            return $this->reviewEvaluationForm;
        }        
    }

    /**
     *
     * @param ReviewEvaluationForm $reviewEvaluationForm 
     */
    public function setReviewEvaluationForm($reviewEvaluationForm) {
        $this->reviewEvaluationForm = $reviewEvaluationForm;
    }

    
    public function execute($request) {
        
        $request->setParameter('initialActionName', 'myPerformanceReview');
        $this->_checkAuthentication($request);
        
        $form = $this->getReviewEvaluationForm();
        
        if ($request->isMethod('post')) {
            $form->bind($request->getParameter($form->getName()));
            if ($form->isValid()) {
               $form->saveForm($request);
               $form->loadFormData();
               $this->getUser()->setFlash('success', __(TopLevelMessages::SAVE_SUCCESS));
            }
        } else {
             $options['id'] = $this->getRequest()->getParameter('id');                
             $form = $this->getReviewEvaluationForm($options);     
             $form->loadFormData();
        }
        $this->form = $form;
    }
}
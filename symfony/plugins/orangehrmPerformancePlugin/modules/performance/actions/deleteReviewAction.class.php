<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of deleteReviewAction
 *
 * @author nadeera
 */

class deleteReviewAction extends basePeformanceAction {
    

    public function preExecute() {
       $this->_checkAuthentication();
    }
    
    public function execute($request) {
          
              
        if ($request->isMethod('post')) {
            $rowsToBeDeleted = $request->getParameter('chkSelectRow');

            if(!empty($rowsToBeDeleted) && sizeof($rowsToBeDeleted)>0){               
                $this->getPerformanceReviewService()->deleteReview($rowsToBeDeleted);
                $this->getUser()->setFlash('success', __(TopLevelMessages::DELETE_SUCCESS));
            } else {
                $this->getUser()->setFlash('warning', __(TopLevelMessages::SELECT_RECORDS));
            }
        }
       
        $this->form = $form;
        $this->redirect('performance/searchPerformancReview');
    }
    
    protected function _checkAuthentication($request = null) {
        $user = $this->getUser()->getAttribute('user');
        if (!($user->isAdmin())) {
            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
        }
    }
}
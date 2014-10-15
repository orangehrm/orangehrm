<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of saveReviewAction
 *
 * @author nadeera
 */
class saveReviewAction extends basePeformanceAction {

    public $saveReviewForm;
    
    public function preExecute() {
        $this->_checkAuthentication();
    }

    /**
     *
     * @return \SaveReviewForm 
     */
    public function getSaveReviewForm() {
        if ($this->saveReviewForm == null) {
            return new SaveReviewForm();
        } else {
            return $this->saveReviewForm;
        }
    }

    /**
     *
     * @param type $saveReviewForm 
     */
    public function setSaveReviewForm($saveReviewForm) {
        $this->saveReviewForm = $saveReviewForm;
    }

    public function execute($request) {
        $request->setParameter('initialActionName', 'searchPerformancReview');
        $form = $this->getSaveReviewForm();

        if ($request->isMethod('post')) {
            $form->bind($request->getParameter($form->getName()));
            if ($form->isValid()) {
                try {
                    if ($form->saveForm($request->getPostParameters())) {
                        $this->getUser()->setFlash('success', __(TopLevelMessages::SAVE_SUCCESS));
                        $this->redirect('performance/searchPerformancReview');
                    } else {
                        $warningForm = $this->getSaveReviewForm();
                        if (!is_null($form->getReview())) {
                            $warningForm->loadFormData($form->getReview()->getId());
                        }
                        $flashMessages = explode('<br/>', __($form->getTemplateMessage()));
                        $this->getUser()->setFlash('warning', $flashMessages);
                        $form = $warningForm;
                    }
                } catch (LeaveAllocationServiceException $e) {
                    $this->templateMessage = array('WARNING', __($e->getMessage()));
                }
            } else {
                $this->templateMessage = array('WARNING', $form->getTemplateMessage());
            }
        } else {
            $form->loadFormData($request->getParameter('hdnEditId'));
        }
        $this->form = $form;
    }
    
    protected function _checkAuthentication($request = null) {
        $user = $this->getUser()->getAttribute('user');
        if (!($user->isAdmin())) {
            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
        }
    }

}

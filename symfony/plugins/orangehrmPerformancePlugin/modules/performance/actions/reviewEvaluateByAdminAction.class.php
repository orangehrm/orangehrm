<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of reviewEvaluateByAdminAction
 *
 * @author nadeera
 */
class reviewEvaluateByAdminAction extends basePeformanceAction {

    public $reviewEvaluationForm;

    /**
     *
     * @return ReviewEvaluationAdminForm 
     */
    public function getReviewEvaluationForm($options = array()) {

        if ($this->reviewEvaluationForm == null) {
            $form = new ReviewEvaluationAdminForm(array(), $options);
            $form->setUser($this->getUser());
            return $form;
        } else {
            return $this->reviewEvaluationForm;
        }
    }

    /**
     *
     * @param ReviewEvaluationAdminForm $reviewEvaluationForm 
     */
    public function setReviewEvaluationForm($reviewEvaluationForm) {
        $this->reviewEvaluationForm = $reviewEvaluationForm;
    }

    public function execute($request) {
        if ($this->checkIsReviwer($request->getParameter('id'))) {
            $request->setParameter('initialActionName', 'searchEvaluatePerformancReview');
            $this->backUrl = 'performance/searchEvaluatePerformancReview';
        } else {
            $request->setParameter('initialActionName', 'searchPerformancReview');
            $this->backUrl = 'performance/searchPerformancReview';
        }

        $this->_checkAuthentication($request);

        $form = $this->getReviewEvaluationForm();

        if ($request->isMethod('post')) {
            $form->bind($request->getParameter($form->getName()));

            if ($form->isValid()) {
                $form->saveForm($request);
                $form->loadFormData();
                $this->getUser()->setFlash('success', __('Successfully Saved'));
            }
        } else {
            $options['id'] = $this->getRequest()->getParameter('id');
            $form = $this->getReviewEvaluationForm($options);
            $form->loadFormData();
        }
        $this->form = $form;
    }

}

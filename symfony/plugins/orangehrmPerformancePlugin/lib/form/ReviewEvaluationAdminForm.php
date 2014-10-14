<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2010 OrangeHRM Inc., http://www.orangehrm.com
 *
 * Please refer the file license/LICENSE.TXT for the license which includes terms and conditions on using this software.
 *
 * */

/**
 * Description of ReviewEvaluationAdminForm
 *
 * @author nadeera
 */
class ReviewEvaluationAdminForm extends ReviewEvaluationForm {

 
    /**
     *
     * @return array
     */
    public function getStylesheets() {
        $styleSheets = parent::getStylesheets();
        $styleSheets[plugin_web_path('orangehrmPerformancePlugin','css/reviewEvaluationSuccess.css')] = 'all';
        $styleSheets[plugin_web_path('orangehrmPerformancePlugin','css/reviewEvaluateByAdminSuccess.css')] = 'all';
        return $styleSheets;
    }

    public function getJavaScripts() {
        $javaScripts = parent::getJavaScripts();
        return $javaScripts;
    }

    /**
     *
     * @return array
     */
    protected function getFormWidgets() {
        $widgets = array(
            'id' => new sfWidgetFormInputHidden(),
            'action' => new sfWidgetFormInputHidden(),
            'evaluationsAction' => new sfWidgetFormInputHidden(),            
            'hrAdminComments' => new sfWidgetFormTextarea(array(), array('rows' => '5')),
            'finalRating' => new sfWidgetFormInput(array(), array('class' => 'formInputText')),
            'completedDate' => new ohrmWidgetDatePicker(array(), array('id' => 'saveReview360Form_workPeriodStartDate'), array('class' => 'formDateInput'))
        );
        return $widgets;
    }

    /**
     *
     * @return array
     */
    protected function getFormValidators() {
       
        $validators = array(
            'id' => new sfValidatorString(array('required' => false)),
            'action' => new sfValidatorString(array('required' => false)),
            'evaluationsAction' => new sfValidatorString(array('required' => false)),
            'hrAdminComments' => new sfValidatorString(array('required' => false)),
            'finalRating' => new sfValidatorString(array('required' => false)),
            'completedDate' => new ohrmDateValidator(array('required' => false))
        );
        return $validators;
    }

    /**
     *
     * @return array
     */
    protected function getFormLabels() {
        $requiredMarker = '&nbsp;<span class="required">*</span>';
        $labels = array(
            'hrAdminComments' => __('Final Comment') . $requiredMarker,
            'finalRating' => __('Final Rating') . $requiredMarker,
            'completedDate' => __('Completed Date') . $requiredMarker
            
        );
        return $labels;
    }

    /**
     *
     * @return boolean 
     */
    public function saveForm($request) {    
        
       $review = parent::saveForm($request);
       if(strlen($this->getValue('hrAdminComments'))>0){
            $review->setFinalComment($this->getValue('hrAdminComments'));
       }
       
       if(strlen($this->getValue('finalRating'))>0){
            $review->setFinalRate(round($this->getValue('finalRating'),2));
       }

       if(strlen($this->getValue('completedDate'))>0){
            $review->setCompletedDate( date( "Y-m-d", strtotime($this->getValue('completedDate'))) );
       }
     
       
       if($this->getValue('evaluationsAction') == "complete"){         
           $review->setStatusId($this->getReviewStatusFactory()->getStatus('approved')->getStatusId());
       }       
       $review->save();      
    }

   

    /**
     *
     * @param integer $id 
     */
    public function loadFormData($id) {
        
        $this->setDefault('id', $this->getReviewId());       
        $this->setDefault('hrAdminComments', $this->getReview()->getFinalComment());
        $this->setDefault('finalRating', $this->getReview()->getFinalRate());
        $this->setDefault('completedDate', set_datepicker_date_format( $this->getReview()->getCompletedDate()) );
       
    }

    /**
     *
     * @return boolean 
     */
    public function isEvaluationsEditable() {
        /* TODO: Control Circle */
        $parameters ['id'] = $this->getReviewId();      

        $review = $this->getPerformanceReviewService()->searchReview($parameters);
        if ( ReviewStatusFactory::getInstance()->getStatus($review->getStatusId())->isEvaluationsEditable()) {
            return true;
        } else {
            return false;
        }
    }  

    /**
     *
     * @return type 
     */
    public function isEditable() {
       return $this->isEvaluationsCompleateEnabled();
    }
    
    /**
     *
     * @return boolean 
     */
    public function isEvaluationsCompleateEnabled() {
        /* TODO: Control Circle */
        $parameters ['id'] = $this->getReviewId();      

        $review = $this->getPerformanceReviewService()->searchReview($parameters);
        if ( ReviewStatusFactory::getInstance()->getStatus($review->getStatusId())->isEvaluationsCompleateEnabled()) {
            return true;
        } else {
            return false;
        }
    }

}
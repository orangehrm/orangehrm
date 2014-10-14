<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 *
 * Description of SaveReviewForm
 *
 * @author nadeera
 */

class SaveReviewForm extends BasePefromanceSearchForm {

    public $review;

    public function getReview() {
        return $this->review;
    }

    public function setReview($review) {
        $this->review = $review;
    }

    /**
     * 
     */
    public function configure() {

        $this->setWidgets($this->getFormWidgets());
        $this->setValidators($this->getFormValidators());
        $this->getWidgetSchema()->setNameFormat('saveReview360Form[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());
    }

    /**
     *
     * @return array
     */
    public function getStylesheets() {
        $styleSheets = parent::getStylesheets();
        $styleSheets[plugin_web_path('orangehrmPerformancePlugin', 'css/saveReviewSuccess.css')] = 'all';
        return $styleSheets;
    }

    /**
     *
     * @return array
     */
    protected function getFormWidgets() {
        $widgets = array(
            'formAction' => new sfWidgetFormInputHidden(),
            'reviewId' => new sfWidgetFormInputHidden(),
            'employeeId' => new sfWidgetFormInputHidden(),
            'employee' => new sfWidgetFormInput(array(), array('class' => 'formInputText')),
            'supervisorReviewerId' => new sfWidgetFormInputHidden(),
            'supervisorReviewer' => new sfWidgetFormInput(array(), array('class' => 'formInputText')),
            'workPeriodStartDate' => new ohrmWidgetDatePicker(array(), array('id' => 'saveReview360Form_workPeriodStartDate'), array('class' => 'formDateInput')),
            'workPeriodEndDate' => new ohrmWidgetDatePicker(array(), array('id' => 'saveReview360Form_workPeriodEndDate'), array('class' => 'formDateInput')),
            'dueDate' => new ohrmWidgetDatePicker(array(), array('id' => 'saveReview360Form_dueDate'), array('class' => 'formDateInput')),
        );
        return $widgets;
    }

    /**
     *
     * @return array
     */
    protected function getFormValidators() {

        $validators = array(
            'formAction' => new sfValidatorString(array('required' => false)),
            'reviewId' => new sfValidatorString(array('required' => false)),
            'employeeId' => new sfValidatorString(array('required' => false)),
            'employee' => new sfValidatorString(array('required' => false)),
            'supervisorReviewerId' => new sfValidatorString(array('required' => false)),
            'supervisorReviewer' => new sfValidatorString(array('required' => false)),
            'workPeriodStartDate' => new ohrmDateValidator(array('required' => false)),
            'workPeriodEndDate' => new ohrmDateValidator(array('required' => false)),
            'dueDate' => new ohrmDateValidator(array('required' => false))
        );

        return $validators;
    }

    /**
     *
     * @return array
     */
    protected function getFormLabels() {
        $requiredMarker = ' <span class="required">*</span>';
        $labels = array(
            'employee' => __("Employee") . $requiredMarker,
            'supervisorReviewer' => __("Supervisor Reviewer") . $requiredMarker,
            'workPeriodStartDate' => __("Work Period Start Date") . $requiredMarker,
            'workPeriodEndDate' => __("Work Period End Date") . $requiredMarker,
            'dueDate' => __("Due Date") . $requiredMarker
        );
        return $labels;
    }

    /**
     *
     * @return type 
     */
    public function saveForm($postData) {

        $formValues = $this->getValues();
        $isSupervisorValid = true;
        $reviewers = array();
        if ($postData['saveReview360Form']['supervisorReviewerId']) {
            $reviewers['supervisors'] = array($postData['saveReview360Form']['supervisorReviewerId']);
        }

        if ($formValues['reviewId'] > 0) {
            $review = $this->getPerformanceReviewService()->searchReview(array('id' => $formValues['reviewId']));            
        } else {
            $review = new PerformanceReview();
            $isSupervisorValid = $this->isSupervisorValid($formValues ['employeeId'], $postData['saveReview360Form']['supervisorReviewerId']);
        }
        if ($isSupervisorValid) {
            $review->setEmployeeNumber($formValues ['employeeId']);
            $review->setWorkPeriodStart(date("Y-m-d", strtotime($formValues ['workPeriodStartDate'])));
            $review->setWorkPeriodEnd(date("Y-m-d", strtotime($formValues ['workPeriodEndDate'])));
            $review->setDueDate(date("Y-m-d", strtotime($formValues ['dueDate'])));

            $employee = $this->getEmployeeService()->getEmployee($formValues ['employeeId']);

            $review->setJobTitleCode($employee->getJobTitleCode());
            $review->setDepartmentId($employee->getWorkStation());
            $review->save();

            $postData['reviewers'] = $reviewers;
            $review = $this->createReviewers($postData['reviewers'], $review);
            $review->save();

            $this->setReview($review);

            if ($formValues['formAction'] == 'save') {
                $review->setStatusId(ReviewStatusInactive::getInstance()->getStatusId());
                $review->save();
            } else if ($formValues['formAction'] == 'activate') {

                $review = $this->createRatings($review);
                $review->save();
                $errorMessages = array();
                $employee = $this->getEmployeeService()->getEmployee($this->getValue('employeeId'));

                if ($employee->getJobTitle()->getId() == null && $employee->getSubDivision()->getId() == null) {
                    $errorMessages [] = __("Cannot activate review for employees who doesn't have Job Title and/or Sub-Division");
                }

                if (!isset($postData['reviewers'])) {
                    $errorMessages [] = __("Cannot activate review without reviewers");
                } else {
                    if (sizeof($review->getReviewerRating()) == 0) {
                        $errorMessages [] = __("Cannot activate review without KPIs");
                    }
                }



                if (sizeof($errorMessages) == 0) {
                    $review->setStatusId(ReviewStatusActivated::getInstance()->getStatusId());
                    $review->setActivatedDate(date("Y-m-d H:i:s"));
                    $review->save();
                    return true;
                } else {
                    $this->setTemplateMessage(implode("<br/>", $errorMessages));
                    $review->setStatusId(ReviewStatusInactive::getInstance()->getStatusId());
                    $review->save();
                    return false;
                }
            }
            return true;
        }else{
            $errorMessages = array();
            $errorMessages [] = __("Invalid Supervisor");
            $this->setTemplateMessage(implode("<br/>", $errorMessages));
        }
    }

    /**
     *
     * @param type $review 
     */
    public function createRatings($review) {

        $jobTitleId = $review->getEmployee()->getJobTitle()->getId();

        $parameters ['jobCode'] = $review->getEmployee()->getJobTitle()->getId();

        $kpis = $this->getKpiService()->searchKpiByJobTitle($parameters);
        foreach ($review->getReviewers() as $reviewer) {
            foreach ($kpis as $kpi) {
                $rating = new ReviewerRating();
                $rating->setReviewId($review->getId());
                $rating->setKpiId($kpi->getId());
                $rating->setReviewerId($reviewer->getId());
                $review->getReviewerRating()->add($rating);
            }
        }
        return $review;
    }

    public function createReviewers($reviewerPostData, $review) {
        if (sizeof($reviewerPostData) > 0) {
            $reviewerGroupFactory = new ReviewerGroupFactory();

            $review->getReviewers()->delete();
            $reviewer = new Reviewer();
            $reviewer->setEmployeeNumber($review->getEmployeeNumber());
            $reviewer->setReviewerGroupId($reviewerGroupFactory->getReviewer('selfReviewer')->getId());
            $reviewer->setReviewId($review->getId());
            $reviewer->setStatus($this->getReviewerReviewStatusFactory()->getStatus('activated')->getStatusId());
            $review->getReviewers()->add($reviewer);

            foreach ($reviewerPostData as $key => $reviewers) {
                foreach ($reviewers as $reviewerId) {
                    $group = $reviewerGroupFactory->getReviewer($key)->getId();
                    $reviewer = new Reviewer();
                    $reviewer->setEmployeeNumber($reviewerId);
                    $reviewer->setReviewerGroupId($group);
                    $reviewer->setReviewId($review->getId());
                    $reviewer->setStatus($this->getReviewerReviewStatusFactory()->getStatus('activated')->getStatusId());
                    $review->getReviewers()->add($reviewer);
                }
            }
        }

        return $review;
    }

    /**
     *
     * @param integer $reviewId 
     */
    public function loadFormData($reviewId) {

        if ($this->getValue('reviewId') > 0) {
            $reviewId = $this->getValue('reviewId');
        }

        if ($reviewId > 0) {

            $review = $this->getPerformanceReviewService()->searchReview(array('id' => $reviewId));

            $this->setDefault('reviewId', $reviewId);
            $this->setDefault('employee', $review->getEmployee()->getFullName());
            $this->setDefault('employeeId', $review->getEmployeeNumber());
            $this->setDefault('workPeriodStartDate', set_datepicker_date_format($review->getWorkPeriodStart()));
            $this->setDefault('workPeriodEndDate', set_datepicker_date_format($review->getWorkPeriodEnd()));
            $this->setDefault('dueDate', set_datepicker_date_format($review->getDueDate()));
        }
    }

    /**
     *
     * @return Doctrine_Collection 
     */
    public function getReviewers($type = null) {
        if ($this->getDefault('reviewId') > 0) {
            $reviewers = $this->getPerformanceReviewService()->searchReview(array('id' => $this->getDefault('reviewId')))->getReviewers();
            $reviewerGroupFactory = new ReviewerGroupFactory();

            if ($type != null) {
                $reviewersArray = array();
                foreach ($reviewers as $reviewer) {
                    if ($reviewerGroupFactory->getReviewer($type)->getId() == $reviewer->getReviewerGroupId()) {
                        $reviewersArray [] = $reviewer;
                    }
                }
                return $reviewersArray;
            } else {
                return $reviewers;
            }
        } else {
            return array();
        }
    }

    /**
     *
     * @return BaseReviewStatus 
     */
    private function getPerformanceReviewStatus() {
        if ($this->getDefault('reviewId') > 0) {
            $review = $this->getPerformanceReviewService()->searchReview(array('id' => $this->getDefault('reviewId')));
            $reviewSatusFactory = new ReviewStatusFactory();
            return $reviewSatusFactory->getStatus($review->getStatusId());
        } else {
            return new ReviewStatusInactive();
        }
    }

    /**
     *
     * @return boolean 
     */
    public function isSaveEnabled() {
        return $this->getPerformanceReviewStatus()->isSaveEnabled();
    }

    /**
     *
     * @return boolean 
     */
    public function isActivateEnabled() {
        return $this->getPerformanceReviewStatus()->isActivateEnabled();
    }

    public function isSupervisorValid($empId, $supervisorId) {
        $supervisorsId = $this->getEmployeeService()->getSupervisorIdListBySubordinateId($empId);
        if (in_array($supervisorId, $supervisorsId)) {
            return true;
        } else {
            return false;
        }
    }

}

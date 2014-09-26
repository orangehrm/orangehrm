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
 * Form class for Save Education
 */
class SaveReviewForm extends BaseForm {

    private $performanceReviewService = null;

    /**
     * 
     * @return type 
     */
    public function getPerformanceReviewService() {
        if (is_null($this->performanceReviewService)) {
            $this->performanceReviewService = new PerformanceReviewService();
            $this->performanceReviewService->setPerformanceReviewDao(new PerformanceReviewDao());
        }
        return $this->performanceReviewService;
    }

    public function configure() {
        $this->setWidgets(array(
            'reviewId' => new sfWidgetFormInputHidden(),
            'employeeName' => new ohrmWidgetEmployeeNameAutoFill(),
            'reviewerName' => new ohrmWidgetEmployeeNameAutoFill(),
            'from_date' => new ohrmWidgetDatePicker(array(), array('id' => 'date_from')),
            'to_date' => new ohrmWidgetDatePicker(array(), array('id' => 'date_to')),
            'dueDate' => new ohrmWIdgetDatePicker(array(), array('id' => 'due_date')),
        ));

        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        $this->setValidators(array(
            'reviewId' => new sfValidatorString(array('required' => false)),
            'employeeName' => new ohrmValidatorEmployeeNameAutoFill(array('required' => true)),
            'reviewerName' => new ohrmValidatorEmployeeNameAutoFill(array('required' => true)),
            'from_date' => new ohrmDateValidator(array('date_format'=>$inputDatePattern, 'required'=>true)),
            'to_date' => new ohrmDateValidator(array('date_format'=>$inputDatePattern, 'required'=>true)),
            'dueDate' => new ohrmDateValidator(array('date_format'=>$inputDatePattern, 'required'=>true)),
        ));

        $this->__setDefaultValues();

        $this->getWidgetSchema()->setLabels($this->getFormLabels());
        $this->widgetSchema->setNameFormat('saveReview[%s]');
    }

    private function __setDefaultValues() {
        $reviewId = $this->getOption('reviewId');
        if (!empty($reviewId)) {
            $review = $this->getPerformanceReviewService()->readPerformanceReview($reviewId);
            $employee = array(
                'empName' => $review->getEmployee()->getFullName(),
                'empId' => $review->getEmployee()->getEmployeeId()
            );
            $reviewer = array(
                'empName' => $review->getReviewer()->getFullName(),
                'empId' => $review->getReviewer()->getEmployeeId()
            );
            $this->setDefaults(array(
                'reviewId' => $review->getId(),
                'employeeName' => $employee,
                'reviewerName' => $reviewer,
                'from_date' => set_datepicker_date_format($review->getPeriodFrom()),
                'to_date' => set_datepicker_date_format($review->getPeriodTo()),
                'dueDate' => set_datepicker_date_format($review->getDueDate()),
            ));
        }
        if ($this->getOption('redirect')) {
            $employee = array(
                'empName' => $this->getOption('empName'),
                'empId' => $this->getOption('empId')
            );
            $reviewer = array(
                'empName' => $this->getOption('reviewerName'),
                'empId' => $this->getOption('reviewerId')
            );

            $this->setDefaults(array(
                'employeeName' => $employee,
                'reviewerName' => $reviewer,
                'from_date' => $this->getOption('toDate'),
                'to_date' => $this->getOption('fromDate'),
                'dueDate' => $this->getOption('dueDate'),
            ));
        }
    }

    protected function getFormLabels() {
        $required = '<em> *</em>';
        $labels = array(
            'employeeName' => __('Employee Name') . $required,
            'reviewerName' => __('Reviewer Name') . $required,
            'from_date' => __('From') . $required,
            'to_date' => __('To') . $required,
            'dueDate' => __('Due Date') . $required,
        );
        return $labels;
    }

    public function getEmployeeListAsJson() {
        $employeeService = new EmployeeService();
        return $employeeService->getEmployeeListAsJson();
    }

    public function save() {
        
    }

}
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
class HolidayForm extends sfForm {

    private $workWeekEntity;
    private $holidayService;
    public $editMode = false;

    /**
     * Holiday form configuration
     */
    public function configure() {

        $holidayPermissions = $this->getDefault('holidayPermissions');
        $id = $this->getDefault('holidayId');

        $widgets = $this->getFormWidgets();

        if (!(($holidayPermissions->canCreate() && empty($id)) || ($holidayPermissions->canUpdate() && $id > 0))) {
            foreach ($widgets as $widgetName => $widget) {
                $widget->setAttribute('disabled', 'disabled');
            }
        }

        $this->setWidgets($widgets);
        $this->setValidators($this->getFormValidators());

        $this->getValidatorSchema()->setOption('allow_extra_fields', true);

        $this->getWidgetSchema()->setLabels($this->getFormLabels());
        $this->getWidgetSchema()->setNameFormat('holiday[%s]');
    }

    /**
     * Set method for Work Week Entity
     * @param WorkWeek $workWeek
     */
    public function setWorkWeekEntity(WorkWeek $workWeek) {
        $this->workWeekEntity = $workWeek;
    }

    /**
     * Get method for Work Week Entity
     * @return WorkWeek workWeekEntity
     */
    public function getWorkWeekEntity() {
        if (!($this->workWeekEntity instanceof WorkWeek)) {
            $this->workWeekEntity = new WorkWeek();
        }
        return $this->workWeekEntity;
    }

    /**
     * Set method for Holiday Service
     * @param HolidayService $holidayService
     */
    public function setHolidayService(HolidayService $holidayService) {
        $this->holidayService = $holidayService;
    }

    /**
     * Get method for Holiday Service
     * @return HolidayService
     */
    public function getHolidayService() {
        if (!($this->holidayService instanceof HolidayService)) {
            $this->holidayService = new HolidayService();
        }
        return $this->holidayService;
    }

    /**
     * Get required days Length List ignore "Weekend"
     */
    public function getDaysLengthList() {
        $fullDaysLengthList = WorkWeek::getDaysLengthList();
        unset($fullDaysLengthList[8]);
        return $fullDaysLengthList;
    }

    /**
     * Set the default values for sfWidgetForm Elements
     * @param integer $holidayId
     */
    public function setDefaultValues($holidayId) {

        $holidayObject = $this->getholidayService()->readHoliday($holidayId);

        if ($holidayObject instanceof Holiday) {
            sfContext::getInstance()->getConfiguration()->loadHelpers('OrangeDate');
            $chkRecurring = $holidayObject->getRecurring() == '1' ? true : false;

            $this->setDefault('id', $holidayObject->getId());
            $this->setDefault('description', $holidayObject->getDescription());
            $this->setDefault('date', set_datepicker_date_format($holidayObject->getDate()));
            $this->setDefault('recurring', $chkRecurring);
            $this->setDefault('length', $holidayObject->getLength());
        }
    }

    /**
     * Check for already added holiday is valid to save and validations are passed
     *
     * @param sfValidatorCallback $validator
     * @param array $values
     */
    public function checkHolidayRules($validator, $values) {
        $date = $values['date'];

        $holidayId = $values['id'];
        $holidayObjectDate = $this->getHolidayService()->readHolidayByDate($date);

        $allowToAdd = true;

        if ($this->editMode) {
            $holidayObject = $this->getHolidayService()->readHoliday($holidayId);
            /* If the selected date is already in a holiday not allow to add */
            if ($holidayObject->getDate() != $date && $date == $holidayObjectDate->getDate()) {
                $allowToAdd = false;
            }
        } else {
            /* Days already added can not be selected to add */
            if ($date == $holidayObjectDate->getDate()) {
                $allowToAdd = false;
            }
        }

        /* Error will not return if the date if not in the correct format */
        if (!$allowToAdd && !is_null($date)) {
            $error = new sfValidatorError($validator, 'Holiday date is in use');
            throw new sfValidatorErrorSchema($validator, array('date' => $error));
        }
        return $values;
    }

    /**
     *
     * @return array
     */
    protected function getFormWidgets() {
        $widgets = array();
        $widgets['id'] = new sfWidgetFormInputHidden();
        $widgets['description'] = new sfWidgetFormInput(array());
        $widgets['date'] = new ohrmWidgetDatePicker(array(), array(
                    'id' => 'holiday_date'
                ));
        $widgets['recurring'] = new sfWidgetFormInputCheckbox(array());
        $widgets['length'] = new sfWidgetFormSelect(array(
                    'choices' => $this->getDaysLengthList(),
                        ), array(
                    'add_empty' => false
                ));

        return $widgets;
    }

    /**
     *
     * @return array
     */
    protected function getFormValidators() {
        $validators = array();

        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();

        $validators['id'] = new sfValidatorString(array('required' => false));
        $validators['recurring'] = new sfValidatorString(array('required' => false));
        $validators['description'] = new sfValidatorString(array(
                    'required' => true,
                    'max_length' => 200,
                        ), array(
                    'required' => 'Holiday Name is required',
                    'max_length' => 'Name of Holiday length exceeded',
                ));
        $validators['date'] = new ohrmDateValidator(
                        array('date_format' => $inputDatePattern,
                            'required' => true)
                        , array(
                    'required' => 'Date field is required',
                    'bad_format' => __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => get_datepicker_date_format(sfContext::getInstance()->getUser()->getDateFormat())))
                ));
        $validators['length'] = new sfValidatorChoice(array('choices' => array_keys($this->getDaysLengthList())));

        return $validators;
    }

    /**
     *
     * @return array
     */
    protected function getFormLabels() {
        $labels = array();


        sfContext::getInstance()->getConfiguration()->loadHelpers('Tag');

        $requiredLabel = '<em>*</em>';

        $labels['description'] = __('Name') . ' ' . $requiredLabel;
        $labels['date'] = __('Date') . ' ' . $requiredLabel;
        $labels['recurring'] = __('Repeats Annually');
        $labels['length'] = __('Full Day/Half Day');

        return $labels;
    }

    public function getJavaScripts() {
        $javaScripts = parent::getJavaScripts();
        $javaScripts[] = plugin_web_path('orangehrmLeavePlugin', 'js/defineHolidaySuccess.js');
        $javaScripts[] = plugin_web_path('orangehrmLeavePlugin', 'js/defineHolidaySuccessValidate.js');

        return $javaScripts;
    }

}


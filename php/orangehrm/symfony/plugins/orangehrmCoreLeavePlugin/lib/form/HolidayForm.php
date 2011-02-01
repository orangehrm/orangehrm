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

class HolidayForm extends sfForm
{

    private $workWeekEntity;
    private $holidayService;

    public $editMode = false;

    /**
     * Setting up the Holiday Form
     */
    public function setup()
    {
        $this->setWorkWeekEntity();
        $this->setHolidayService();
    }

    /**
     * Holiday form configuration
     */
    public function configure()
    {

        $this->setWidgets(array(
                'hdnHolidayId' => new sfWidgetFormInputHidden(),
                'txtDescription' => new sfWidgetFormInput(),
                'txtDate' => new sfWidgetFormInput(),
                'chkRecurring' => new sfWidgetFormInputCheckbox(),
                'selLength' => new sfWidgetFormSelect( array('choices'=>$this->getDaysLengthList()), array('add_empty'=>false)),
        ));

        $this->setValidators(array(
                'hdnHolidayId' => new sfValidatorString(array('required' => false)),
                'chkRecurring' => new sfValidatorString(array('required' => false)),
                'txtDescription' => new sfValidatorString(array('required' => true, 'max_length'=>200),array('required'=>'Holiday Name is required', 'max_length'=>'Name of Holiday length exceeded')),
                'txtDate' => new sfValidatorRegex(array('pattern'=>"/^(19|20)\d\d-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/",'required'=>true),array('required'=>'Date field is required', 'invalid'=>'Date format should be YYYY-MM-DD')),

                'selLength' => new sfValidatorChoice(array('choices' => array_keys($this->getDaysLengthList())))
        ));

        $this->validatorSchema->setOption('allow_extra_fields', true);
        //$this->validatorSchema->setPostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkHolidayRules'))));
        
        $this->widgetSchema->setNameFormat('holiday[%s]');

    }

    /**
     * Set method for Work Week Entity
     *
     */
    public function setWorkWeekEntity(){

        $this->workWeekEntity = new WorkWeek();

    }

    /**
     * Get method for Work Week Entity
     *
     * @return WorkWeek workWeekEntity
     */

    public function getWorkWeekEntity(){

        return $this->workWeekEntity;

    }


    /**
     * Set method for Holiday Service
     *
     */
    public function setHolidayService(){
        $this->holidayService = new HolidayService();
    }

    /**
     * Get method for Holiday Service
     *
     * @return HolidayService holidayService
     */

    public function getHolidayService(){

        return $this->holidayService;

    }


    /**
     * get required days Length List ignore "Weekend"
     */
    public function getDaysLengthList()
    {
        $fullDaysLengthList = $this->getWorkWeekEntity()->getDaysLengthList();
        unset($fullDaysLengthList[8]);
        return $fullDaysLengthList;
    }

    /**
     * Set the default values for sfWidgetForm Elements
     * @param integer $holidayId
     */
    public function setDefaultValues($holidayId)
    {

        $holidayObject = $this->getholidayService()->readHoliday($holidayId);

        if ($holidayObject instanceof Holiday)
        {

            $this->setDefault('hdnHolidayId', $holidayObject->getHolidayId());
            $this->setDefault('txtDescription', $holidayObject->getDescription());
            $this->setDefault('txtDate', $holidayObject->getDate());
            $chkRecurring = $holidayObject->getRecurring()=='1'?true:false;
            $this->setDefault('chkRecurring', $chkRecurring);
            $this->setDefault('selLength', $holidayObject->getLength());

        }

    }

    /**
     * Check for already added holiday is valid to save and validations are passed
     *
     * @param sfValidatorCallback $validator
     * @param array $values
     */
    public function checkHolidayRules($validator, $values)
    {
        $date = $values['txtDate'];
        $hid = $values['hdnHolidayId'];
        // read the holiday by date
        $holidayObjectDate = $this->getHolidayService()->readHolidayByDate($date);

        $allowToAdd = true;

        if($this->editMode)
        {
            $holidayObject = $this->getHolidayService()->readHoliday($hid);
            // if the selected date is already in a holiday not allow to add
            if($holidayObject->getDate() != $date && $date == $holidayObjectDate->getDate() )
            {
                $allowToAdd = false;
            }
        }else{
            // days already added can not be selected to add
            if($date == $holidayObjectDate->getDate()){
                $allowToAdd = false;
            }
        }
        
        // Error will not return if the date if not in the correct format
        if(!$allowToAdd && !is_null($date))
        {
            $error = new sfValidatorError($validator, 'Holiday date is in use' );
            throw new sfValidatorErrorSchema($validator, array('txtDate' => $error));
            
        }
        return $values;
    }

}


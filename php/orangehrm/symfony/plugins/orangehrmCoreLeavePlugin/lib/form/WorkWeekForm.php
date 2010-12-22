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

class WorkWeekForm extends sfForm {

    private $workWeekService;
    private $workWeekEntity;

    /**
     * Set method for Work Week Service
     *
     */
    public function setWorkWeekService(){

        $this->workWeekService = new WorkWeekService();
        $this->workWeekService->setWorkWeekDao(new WorkWeekDao());
        
    }

    /**
     * Get method for work week Service
     *
     * @return WorkWeekService workWeekService
     */

    public function getWorkWeekService(){

        return $this->workWeekService;
        
    }

    /**
     * Set method for work week Entity
     *
     */
    public function setWorkWeekEntity(){

        $this->workWeekEntity = new WorkWeek();
        
    }

    /**
     * Get method for work week Entity
     *
     * @return WorkWeek workWeekEntity
     */

    public function getWorkWeekEntity(){

        return $this->workWeekEntity;

    }


    /**
     * Setup method of the form
     */

    public function setUp(){
        $this->setWorkWeekService(); // set work week Service
        $this->setWorkWeekEntity();  // set work week Entity
    }

    /**
     * Configuring WorkWeek form widget
     */
    public function configure() {

        $formWidgets = array();
        $formValidators = array();
        $formDefaults = array();
        $formLabels = array();

        $workWeekList = $this->getWorkWeekService()->getWorkWeekList(0,7); // only 7 days a week

        foreach ($workWeekList as $key => $workWeek) {

            // set form widget Array
            $formWidgets["select_" . $workWeek->getDay()] = new sfWidgetFormSelect( array('choices' => $this->getWorkWeekEntity()->getDaysLengthList()) );

            // set form validation array
            $formValidators["select_" .$workWeek->getDay()] = new sfValidatorChoice( array('choices'=>array_keys( $this->getWorkWeekEntity()->getDaysLengthList())), array('invalid'=> "Invalid work week for ". $this->getWorkWeekEntity()->getDayById($workWeek->getDay()) ) );

            // set Default Values
            $formDefaults["select_" . $workWeek->getDay()] = $workWeek->getLength();

            // set Label texts
            $formLabels["select_" . $workWeek->getDay()] = $this->getWorkWeekEntity()->getDayById($workWeek->getDay());

        }

        $this->setValidators($formValidators);
        $this->setWidgets($formWidgets);
        $this->setDefaults($formDefaults);

        $this->widgetSchema->setLabels($formLabels);
        $this->widgetSchema->setNameFormat('WorkWeek[%s]');
        $this->validatorSchema->setPostValidator(new sfValidatorCallback(array('callback' => array($this, 'validateWorkWeekValue'))));

    }

    /**
     * Read WorkWeek Objects
     *
     * @param string $data
     *
     * @return array Array of WorkWeek objects
     * 
     */
    public function getWorkWeekObjects($data) {

        $daysList = $this->getWorkWeekEntity()->getDaysList();
        $workWeekList = array();

        foreach($data as $day => $length) { 
            $fday = substr($day, -1); // strip "select_" in the day param
            if(array_key_exists($fday, $daysList)) {
                $workWeek = $this->getWorkWeekService()->readWorkWeek($fday);
                $workWeek->setLength($length);
                $workWeekList[] =  $workWeek; // this will return only allowed work week objects
            }else {
                throw new LeaveServiceException("Invaid Day");
            }
        }
        return $workWeekList;

    }

    /**
     * Validate WorkWeek form elements passed by the view // prevent form element alteration
     *
     * @param sfValidator $validator
     * @param array $values
     * @return array $values Array of Values
     */
    public function validateWorkWeekValue($validator, $values) {

        $daysList = $this->getWorkWeekEntity()->getDaysList();
        $workWeekList = array();

        foreach($values as $day => $length) {
            if(preg_match("/select_[1-7]{1}$/",$day) ) {
                $fday = substr($day, -1); // strip "select_" in the day param
                if(!array_key_exists($fday, $daysList)) {
                    $error = new sfValidatorError($validator, 'Invalid WorkWeek!' );
                    throw new sfValidatorErrorSchema($validator, array($day => $error));
                }

            }
        }
        return $values;
        
    }

}

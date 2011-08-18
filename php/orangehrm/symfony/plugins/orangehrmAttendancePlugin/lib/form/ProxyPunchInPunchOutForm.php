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
class ProxyPunchInPunchOutForm extends AttendanceForm {

    public function configure() {

        $this->formWidgets['timezone'] = new sfWidgetFormSelect(array('choices' => $this->getTimezoneArray()), array('class' => 'timezone'));
        $this->setWidgets($this->formWidgets);


        $this->formValidators['timezone'] = new sfValidatorString(array(), array('required' => __('Enter timezone')));
        $this->setValidators($this->formValidators);

        $this->widgetSchema->setNameFormat('attendance[%s]');
        parent::configure();
    }
    
    
     public function getTimezoneArray() {


        $this->timezoneArray[0] = 'GMT';
        $this->timezoneArray[1] = '+1.00';
        $this->timezoneArray[2] = '+2.00';
        $this->timezoneArray[3] = '+3.00';
        $this->timezoneArray[4] = '+4.00';
        $this->timezoneArray[5] = '+5.00';
        $this->timezoneArray[6] = '+5.50';
        $this->timezoneArray[7] = '+6.00';
        $this->timezoneArray[8] = '+7.00';
        $this->timezoneArray[9] = '+8.00';
        $this->timezoneArray[10] = '+9.00';
        $this->timezoneArray[11] = '+9.50';
        $this->timezoneArray[12] = '+10.00';
        $this->timezoneArray[13] = '+11.00';
        $this->timezoneArray[14] = '+12.00';
        $this->timezoneArray[15] = '-11.00';
        $this->timezoneArray[16] = '-10.00';
        $this->timezoneArray[17] = '-9.00';
        $this->timezoneArray[18] = '-8.00';
        $this->timezoneArray[19] = '-7.00';
        $this->timezoneArray[20] = '-6.00';
        $this->timezoneArray[21] = '-7.00';
        $this->timezoneArray[22] = '-5.00';
        $this->timezoneArray[23] = '-4.00';
        $this->timezoneArray[24] = '-3.50';
        $this->timezoneArray[25] = '-3.00';
        $this->timezoneArray[26] = '-1.00';
        
        return $this->timezoneArray;
    }

}

?>

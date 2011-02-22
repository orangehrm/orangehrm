<?php
/*
// OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
// all the essential functionalities required for any enterprise.
// Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com

// OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
// the GNU General Public License as published by the Free Software Foundation; either
// version 2 of the License, or (at your option) any later version.

// OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
// without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU General Public License for more details.

// You should have received a copy of the GNU General Public License along with this program;
// if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
// Boston, MA  02110-1301, USA
*/

/**
 * Form class for employee contact detail
 */
class EmployeeEmergencyContactForm extends BaseForm {

    public function configure() {

        $empNumber = $this->getOption('empNumber');
        
        // Note: Widget names were kept from old non-symfony version
        $this->setWidgets(array(
            'empNumber' => new sfWidgetFormInputHidden(array(), 
                    array('value' => $empNumber)),
            'seqNo' => new sfWidgetFormInputHidden(), // seq no
            'name' => new sfWidgetFormInputText(),
            'relationship' => new sfWidgetFormInputText(),
            'homePhone' => new sfWidgetFormInputText(),
            'mobilePhone' => new sfWidgetFormInputText(),
            'workPhone' => new sfWidgetFormInputText(),

        ));

        $this->setValidators(array(
            'empNumber' => new sfValidatorNumber(array('required' => true, 'min'=> 0)),
            'seqNo' => new sfValidatorNumber(array('required' => false, 'min' => 1)),
            'name' => new sfValidatorString(array('required' => true)),
            'relationship' => new sfValidatorString(array('required' => true)),
            'homePhone' => new sfValidatorString(array('required' => false)),
            'mobilePhone' => new sfValidatorString(array('required' => false)),
            'workPhone' => new sfValidatorString(array('required' => false))
        ));


        // set up your post validator method
        $this->validatorSchema->setPostValidator(
          new sfValidatorCallback(array(
            'callback' => array($this, 'postValidate')
          ))
        );

        $this->widgetSchema->setNameFormat('emgcontacts[%s]');
    }

    public function postValidate($validator, $values) {

        $homePhone = $values['homePhone'];
        $mobile = $values['mobilePhone'];
        $workPhone = $values['workPhone'];

        if (empty($homePhone) && empty($mobile) && empty($workPhone)) {

            $message = sfContext::getInstance()->getI18N()->__('Please specify at least one phone number.');
            $error = new sfValidatorError($validator, $message);
            throw new sfValidatorErrorSchema($validator, array('' => $error));

        }
        
        return $values;
    }


    /**
     * Save employee contract
     */
    public function save() {

        $empNumber = $this->getValue('empNumber');
        $seqNo = $this->getValue('seqNo');

        $emergencyContact = false;

        if (empty($seqNo)) {

            $q = Doctrine_Query::create()
                    ->select('MAX(ec.seqno)')
                    ->from('EmpEmergencyContact ec')
                    ->where('ec.emp_number = ?', $empNumber);
            $result = $q->execute(array(), Doctrine::HYDRATE_ARRAY);

            if (count($result) != 1) {
                throw new PIMServiceException('MAX(seqno) failed.');
            }
            $seqNo = is_null($result[0]['MAX']) ? 1 : $result[0]['MAX'] + 1;

        } else {
            $emergencyContact = Doctrine::getTable('EmpEmergencyContact')->find(array('emp_number' => $empNumber,
                                                                                'seqno' => $seqNo));

            if ($emergencyContact == false) {
                throw new PIMServiceException('Invalid emergency contact');
            }
        }

        if ($emergencyContact === false) {
            $emergencyContact = new EmpEmergencyContact();
            $emergencyContact->emp_number = $empNumber;
            $emergencyContact->seqno = $seqNo;
        }

        $emergencyContact->name = $this->getValue('name');
        $emergencyContact->relationship = $this->getValue('relationship');
        $emergencyContact->home_phone = $this->getValue('homePhone');
        $emergencyContact->mobile_phone = $this->getValue('mobilePhone');
        $emergencyContact->office_phone = $this->getValue('workPhone');

        $emergencyContact->save();
    }

}


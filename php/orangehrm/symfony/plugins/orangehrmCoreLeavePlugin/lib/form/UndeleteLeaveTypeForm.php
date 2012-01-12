<?php

class UndeleteLeaveTypeForm extends orangehrmForm {

    public function configure() {

        $this->setWidget('undeleteId', new sfWidgetFormInputHidden());
        $this->setValidator('undeleteId', new sfValidatorString(array('required' => true)));
        $this->widgetSchema->setNameFormat('undeleteLeaveType[%s]');
    }

}



?>

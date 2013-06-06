<?php

class SystemCheck extends sfForm {

    public function configure() {
        $this->setWidgets(array(
            'submitBy' => new sfWidgetFormInputHidden(array(), array('value' => 'systemCheck'))
        ));
        
        $this->widgetSchema->setNameFormat('systemCheck[%s]');
        
        $this->setValidators(array(
            'submitBy' => new sfValidatorString(array('required' => true))
        ));
    }

}
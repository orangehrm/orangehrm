<?php

class ConfigureFile extends sfForm {

    public function configure() {
        $this->setWidgets(array(
            'submitBy' => new sfWidgetFormInputHidden(array(), array('value' => 'configureFile'))
        ));
        
        $this->widgetSchema->setNameFormat('configureFile[%s]');
        
        $this->setValidators(array(
            'submitBy' => new sfValidatorString(array('required' => true))
        ));
    }

}

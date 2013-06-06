<?php

class DatabaseChange extends sfForm {

    public function configure() {
        
        $this->setWidgets(array(
            'submitBy' => new sfWidgetFormInputHidden(array(), array('value' => 'dbChange'))
        ));
        
        $this->widgetSchema->setNameFormat('databaseChange[%s]');
        
        $this->setValidators(array(
            'submitBy' => new sfValidatorString(array('required' => true))
        ));
    }

}

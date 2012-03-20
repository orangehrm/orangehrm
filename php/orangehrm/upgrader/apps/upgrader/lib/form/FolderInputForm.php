<?php

class FolderInput extends sfForm {

    public function configure() {
        $this->setWidgets(array(
            'folder_path' => new sfWidgetFormInputText(array(), array())
        ));
        
        $this->widgetSchema->setLabels(array(
          'folder_path'    => 'Folder Path'
        ));
        $this->widgetSchema->setNameFormat('folderPath[%s]');
        
        $this->setValidators(array(
            'folder_path' => new sfValidatorString(array('required' => true), array('required' => 'Folder Path is Empty'))
        ));
    }

}

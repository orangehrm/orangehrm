<?php

class SubunitForm extends ohrmFormComponent {

    public function configure() {
        $properties = new ohrmFormComponentProperty();

        $properties->setService(new CompanyStructureService());
        $properties->setMethod('getSubunit');
        $properties->setParameters(array(1));
        $properties->setFields(array(
            'Id' => 'getId',
            'Unit Id' => 'getUnitId',
            'Name' => 'getName',
            'Description' => 'getDescription',
            'Parent' => ''
        ));

        $properties->setIdField('Id');

        $properties->setFormStyle('width: auto; max-width: 600px;');

        $properties->setFieldTypes(array(
            'Parent' => 'hidden',            
            'Id' => 'hidden',     
            'Description' => 'textarea'
        ));

        $properties->setRequiredFields(array('Name'));

        $this->setPropertyObject($properties);

        $this->hasFormNavigatorBar(false);
    }

}
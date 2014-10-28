<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ohrmWidgetJQueryDateRange
 *
 * @author orangehrm
 */
class ohrmWidgetJQueryDateRange extends ohrmWidgetDatePickerOld implements ohrmEmbeddableWidget {

    public function embedWidgetIntoForm(sfForm $form) {

        $widgetSchema = $form->getWidgetSchema();
        $validatorSchema = $form->getValidatorSchema();

        $widgetSchema['from_date'] = new ohrmWidgetDatePickerOld(array(), array('id' => 'from_date'));
        $widgetSchema['from_date']->setLabel("From ");
        $form->setValidator('from_date', new sfValidatorDate());


        $widgetSchema['to_date'] = new ohrmWidgetDatePickerOld(array(), array('id' => 'to_date'));
        $widgetSchema['to_date']->setLabel("To ");
        $form->setValidator('to_date', new sfValidatorDate());

        $validatorSchema->setPostValidator(new sfValidatorSchemaCompare('from_date', sfValidatorSchemaCompare::LESS_THAN_EQUAL, 'to_date',
                        array('throw_global_error' => true),
                        array('invalid' => 'The from date ("%left_field%") must be before the to date ("%right_field%")')
        ));

        return $form;
    }

}

?>

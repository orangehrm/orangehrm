<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ohrmWidgetReportDisplayGroup
 *
 * @author ruchira
 */
class ohrmWidgetReportDisplayGroup extends sfWidgetFormInputCheckbox implements ohrmEmbeddableWidget{

    
  public function __construct($options = array(), $attributes = array()) {

    parent::__construct($options, $attributes);
  }
  
    /**
     * Embeds this widget into the form. Sets label and validator for this widget.
     * @param sfForm $form
     */
    public function embedWidgetIntoForm(sfForm &$form) {


        $widgetSchema = $form->getWidgetSchema();
//        $validatorSchema = $form->getValidatorSchema();

        $widgetSchema[$this->attributes['id']] = $this;
//        $widgetSchema[$this->attributes['id']]->setLabel(ucwords(str_replace("_", " ", $this->attributes['id'])));
    }    
}

?>

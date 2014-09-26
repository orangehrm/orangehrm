<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TimesheetRowForm
 *
 * @author orangehrm
 */
class TimesheetRowForm extends sfForm {


    public function configure() {

	$noOfDays = $this->getOption('noOfDays');
	$widgetArray = array(
	    'toDelete' => new sfWidgetFormInputCheckbox(array(), array('class' => 'toDelete')),
            'projectName' => new sfWidgetFormInputText(array(), array('align' => 'center', 'class' => 'project')),
            'projectId' => new sfWidgetFormInputHidden(),
            'projectActivityName' => new sfWidgetFormSelect(array('choices' => array('-1' => '-- ' . __('Select') . ' --')), array('class' => 'projectActivity')),
            'projectActivityId' => new sfWidgetFormInputHidden()
	);
		    for ($i = 0; $i < $noOfDays; $i++) {
			$widgetArray[$i] = new sfWidgetFormInputText(array(), array('align' => 'center', 'class' => 'items'));
			$widgetArray['TimesheetItemId'.$i] = new sfWidgetFormInputHidden();
		    }
        $this->setWidgets($widgetArray);

        $this->widgetSchema->setNameFormat('time[%s]');

        $this->widgetSchema['projectName']->setAttribute('size', 35);
        $this->setDefault('projectName', __('Type for hints').'...');
        $this->widgetSchema['projectActivityName']->setAttribute('style', 'width:225px');
	for ($i = 0; $i < $noOfDays; $i++) {
		$this->widgetSchema[$i]->setAttribute('size', 2);
	}

	$validatorsArray = array(
	    'toDelete' => new sfValidatorPass(array('required' => false)),
            'projectName' => new sfValidatorString(array('required' => true), array('required' => __('Required'))),
            'projectId' => new sfValidatorInteger(array('required' => true)),
            'projectActivityName' => new sfValidatorPass(array('required' => false)),
            'projectActivityId' => new sfValidatorInteger(array('required' => false))
	);

	for ($i = 0; $i < $noOfDays; $i++) {
			$validatorsArray[$i] = new sfValidatorNumber(array('required' => false));
			$validatorsArray['TimesheetItemId'.$i] =  new sfValidatorInteger(array('required' => false));
	}


        $this->setValidators($validatorsArray);
    }

}


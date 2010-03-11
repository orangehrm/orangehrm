<?php

/**
 * EmpLocations form base class.
 *
 * @package    form
 * @subpackage emp_locations
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseEmpLocationsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'emp_number' => new sfWidgetFormInputHidden(),
      'loc_code'   => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'emp_number' => new sfValidatorDoctrineChoice(array('model' => 'EmpLocations', 'column' => 'emp_number', 'required' => false)),
      'loc_code'   => new sfValidatorDoctrineChoice(array('model' => 'EmpLocations', 'column' => 'loc_code', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('emp_locations[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EmpLocations';
  }

}

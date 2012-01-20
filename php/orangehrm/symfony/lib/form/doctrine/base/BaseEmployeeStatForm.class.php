<?php

/**
 * EmployeeStat form base class.
 *
 * @package    form
 * @subpackage employee_stat
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseEmployeeStatForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'estat_code' => new sfWidgetFormInputHidden(),
      'estat_name' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'estat_code' => new sfValidatorDoctrineChoice(array('model' => 'EmployeeStat', 'column' => 'estat_code', 'required' => false)),
      'estat_name' => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('employee_stat[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EmployeeStat';
  }

}

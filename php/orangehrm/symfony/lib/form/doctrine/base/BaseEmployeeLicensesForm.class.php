<?php

/**
 * EmployeeLicenses form base class.
 *
 * @package    form
 * @subpackage employee_licenses
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseEmployeeLicensesForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'empNumber'             => new sfWidgetFormInputHidden(),
      'licenses_code'         => new sfWidgetFormInputHidden(),
      'licenses_date'         => new sfWidgetFormDate(),
      'licenses_renewal_date' => new sfWidgetFormDate(),
    ));

    $this->setValidators(array(
      'empNumber'             => new sfValidatorDoctrineChoice(array('model' => 'EmployeeLicenses', 'column' => 'emp_number', 'required' => false)),
      'licenses_code'         => new sfValidatorDoctrineChoice(array('model' => 'EmployeeLicenses', 'column' => 'licenses_code', 'required' => false)),
      'licenses_date'         => new sfValidatorDate(array('required' => false)),
      'licenses_renewal_date' => new sfValidatorDate(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('employee_licenses[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EmployeeLicenses';
  }

}

<?php

/**
 * EmployeeMemberDetail form base class.
 *
 * @package    form
 * @subpackage employee_member_detail
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseEmployeeMemberDetailForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'emp_number'                => new sfWidgetFormInputHidden(),
      'membship_code'             => new sfWidgetFormInputHidden(),
      'membtype_code'             => new sfWidgetFormInputHidden(),
      'ememb_subscript_amount'    => new sfWidgetFormInput(),
      'ememb_subscript_ownership' => new sfWidgetFormInput(),
      'ememb_commence_date'       => new sfWidgetFormDateTime(),
      'ememb_renewal_date'        => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'emp_number'                => new sfValidatorDoctrineChoice(array('model' => 'EmployeeMemberDetail', 'column' => 'emp_number', 'required' => false)),
      'membship_code'             => new sfValidatorDoctrineChoice(array('model' => 'EmployeeMemberDetail', 'column' => 'membship_code', 'required' => false)),
      'membtype_code'             => new sfValidatorDoctrineChoice(array('model' => 'EmployeeMemberDetail', 'column' => 'membtype_code', 'required' => false)),
      'ememb_subscript_amount'    => new sfValidatorNumber(array('required' => false)),
      'ememb_subscript_ownership' => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'ememb_commence_date'       => new sfValidatorDateTime(array('required' => false)),
      'ememb_renewal_date'        => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('employee_member_detail[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EmployeeMemberDetail';
  }

}

<?php

/**
 * EmployeeMemberDetail form base class.
 *
 * @method EmployeeMemberDetail getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseEmployeeMemberDetailForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'emp_number'    => new sfWidgetFormInputHidden(),
      'membship_code' => new sfWidgetFormInputHidden(),
      'membtype_code' => new sfWidgetFormInputHidden(),
      'subscription'  => new sfWidgetFormInputText(),
      'ownership'     => new sfWidgetFormInputText(),
      'commence_date' => new sfWidgetFormDateTime(),
      'renewal_date'  => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'emp_number'    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('emp_number')), 'empty_value' => $this->getObject()->get('emp_number'), 'required' => false)),
      'membship_code' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('membship_code')), 'empty_value' => $this->getObject()->get('membship_code'), 'required' => false)),
      'membtype_code' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('membtype_code')), 'empty_value' => $this->getObject()->get('membtype_code'), 'required' => false)),
      'subscription'  => new sfValidatorNumber(array('required' => false)),
      'ownership'     => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'commence_date' => new sfValidatorDateTime(array('required' => false)),
      'renewal_date'  => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('employee_member_detail[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'EmployeeMemberDetail';
  }

}

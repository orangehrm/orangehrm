<?php

/**
 * EmployeeLicenses form base class.
 *
 * @method EmployeeLicenses getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseEmployeeLicensesForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'empNumber'    => new sfWidgetFormInputHidden(),
      'code'         => new sfWidgetFormInputHidden(),
      'date'         => new sfWidgetFormDate(),
      'renewal_date' => new sfWidgetFormDate(),
    ));

    $this->setValidators(array(
      'empNumber'    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('empNumber')), 'empty_value' => $this->getObject()->get('empNumber'), 'required' => false)),
      'code'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('code')), 'empty_value' => $this->getObject()->get('code'), 'required' => false)),
      'date'         => new sfValidatorDate(array('required' => false)),
      'renewal_date' => new sfValidatorDate(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('employee_licenses[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'EmployeeLicenses';
  }

}

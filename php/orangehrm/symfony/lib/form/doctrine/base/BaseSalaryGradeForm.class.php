<?php

/**
 * SalaryGrade form base class.
 *
 * @method SalaryGrade getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseSalaryGradeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'sal_grd_code' => new sfWidgetFormInputHidden(),
      'sal_grd_name' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'sal_grd_code' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('sal_grd_code')), 'empty_value' => $this->getObject()->get('sal_grd_code'), 'required' => false)),
      'sal_grd_name' => new sfValidatorString(array('max_length' => 60, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('salary_grade[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'SalaryGrade';
  }

}

<?php

/**
 * SalaryGrade form base class.
 *
 * @package    form
 * @subpackage salary_grade
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseSalaryGradeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'sal_grd_code' => new sfWidgetFormInputHidden(),
      'sal_grd_name' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'sal_grd_code' => new sfValidatorDoctrineChoice(array('model' => 'SalaryGrade', 'column' => 'sal_grd_code', 'required' => false)),
      'sal_grd_name' => new sfValidatorString(array('max_length' => 60, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('salary_grade[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'SalaryGrade';
  }

}

<?php

/**
 * JobTitle form base class.
 *
 * @package    form
 * @subpackage job_title
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseJobTitleForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'name'          => new sfWidgetFormInput(),
      'description'   => new sfWidgetFormInput(),
      'comments'      => new sfWidgetFormTextarea(),
      'salaryGradeId' => new sfWidgetFormInput(),
      'jobspecId'     => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorDoctrineChoice(array('model' => 'JobTitle', 'column' => 'jobtit_code', 'required' => false)),
      'name'          => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'description'   => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'comments'      => new sfValidatorString(array('max_length' => 400, 'required' => false)),
      'salaryGradeId' => new sfValidatorString(array('max_length' => 13, 'required' => false)),
      'jobspecId'     => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('job_title[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'JobTitle';
  }

}

<?php

/**
 * JobCategory form base class.
 *
 * @package    form
 * @subpackage job_category
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseJobCategoryForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'eec_code' => new sfWidgetFormInputHidden(),
      'eec_desc' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'eec_code' => new sfValidatorDoctrineChoice(array('model' => 'JobCategory', 'column' => 'eec_code', 'required' => false)),
      'eec_desc' => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('job_category[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'JobCategory';
  }

}

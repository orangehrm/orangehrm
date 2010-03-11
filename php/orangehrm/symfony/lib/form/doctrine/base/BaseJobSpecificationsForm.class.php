<?php

/**
 * JobSpecifications form base class.
 *
 * @package    form
 * @subpackage job_specifications
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseJobSpecificationsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'jobspec_id'     => new sfWidgetFormInputHidden(),
      'jobspec_name'   => new sfWidgetFormInput(),
      'jobspec_desc'   => new sfWidgetFormTextarea(),
      'jobspec_duties' => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'jobspec_id'     => new sfValidatorDoctrineChoice(array('model' => 'JobSpecifications', 'column' => 'jobspec_id', 'required' => false)),
      'jobspec_name'   => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'jobspec_desc'   => new sfValidatorString(array('max_length' => 2147483647, 'required' => false)),
      'jobspec_duties' => new sfValidatorString(array('max_length' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('job_specifications[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'JobSpecifications';
  }

}

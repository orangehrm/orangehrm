<?php

/**
 * JobSpecifications form base class.
 *
 * @method JobSpecifications getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseJobSpecificationsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'jobspec_id'     => new sfWidgetFormInputHidden(),
      'jobspec_name'   => new sfWidgetFormInputText(),
      'jobspec_desc'   => new sfWidgetFormTextarea(),
      'jobspec_duties' => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'jobspec_id'     => new sfValidatorChoice(array('choices' => array($this->getObject()->get('jobspec_id')), 'empty_value' => $this->getObject()->get('jobspec_id'), 'required' => false)),
      'jobspec_name'   => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'jobspec_desc'   => new sfValidatorString(array('max_length' => 2147483647, 'required' => false)),
      'jobspec_duties' => new sfValidatorString(array('max_length' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('job_specifications[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'JobSpecifications';
  }

}

<?php

/**
 * ProjectActivity form base class.
 *
 * @method ProjectActivity getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseProjectActivityForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'activity_id' => new sfWidgetFormInputHidden(),
      'project_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Project'), 'add_empty' => false)),
      'deleted'     => new sfWidgetFormInputText(),
      'name'        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'activity_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('activity_id')), 'empty_value' => $this->getObject()->get('activity_id'), 'required' => false)),
      'project_id'  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Project'))),
      'deleted'     => new sfValidatorInteger(array('required' => false)),
      'name'        => new sfValidatorString(array('max_length' => 100, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('project_activity[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProjectActivity';
  }

}

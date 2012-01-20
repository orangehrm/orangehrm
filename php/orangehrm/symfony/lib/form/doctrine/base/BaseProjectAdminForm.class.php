<?php

/**
 * ProjectAdmin form base class.
 *
 * @method ProjectAdmin getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseProjectAdminForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'project_id' => new sfWidgetFormInputHidden(),
      'emp_number' => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'project_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('project_id')), 'empty_value' => $this->getObject()->get('project_id'), 'required' => false)),
      'emp_number' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('emp_number')), 'empty_value' => $this->getObject()->get('emp_number'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('project_admin[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProjectAdmin';
  }

}

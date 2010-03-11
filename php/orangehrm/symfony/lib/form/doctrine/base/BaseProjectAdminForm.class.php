<?php

/**
 * ProjectAdmin form base class.
 *
 * @package    form
 * @subpackage project_admin
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseProjectAdminForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'project_id' => new sfWidgetFormInputHidden(),
      'emp_number' => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'project_id' => new sfValidatorDoctrineChoice(array('model' => 'ProjectAdmin', 'column' => 'project_id', 'required' => false)),
      'emp_number' => new sfValidatorDoctrineChoice(array('model' => 'ProjectAdmin', 'column' => 'emp_number', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('project_admin[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProjectAdmin';
  }

}

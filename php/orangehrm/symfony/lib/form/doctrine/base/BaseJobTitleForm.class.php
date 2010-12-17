<?php

/**
 * JobTitle form base class.
 *
 * @method JobTitle getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseJobTitleForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'name'          => new sfWidgetFormInputText(),
      'description'   => new sfWidgetFormInputText(),
      'comments'      => new sfWidgetFormTextarea(),
      'salaryGradeId' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('SalaryGrade'), 'add_empty' => true)),
      'jobspecId'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('JobSpecifications'), 'add_empty' => true)),
      'isActive'      => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'name'          => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'description'   => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'comments'      => new sfValidatorString(array('max_length' => 400, 'required' => false)),
      'salaryGradeId' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('SalaryGrade'), 'required' => false)),
      'jobspecId'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('JobSpecifications'), 'required' => false)),
      'isActive'      => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('job_title[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'JobTitle';
  }

}

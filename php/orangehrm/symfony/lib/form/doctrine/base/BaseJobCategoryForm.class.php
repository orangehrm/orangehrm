<?php

/**
 * JobCategory form base class.
 *
 * @method JobCategory getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseJobCategoryForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'eec_code' => new sfWidgetFormInputHidden(),
      'eec_desc' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'eec_code' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('eec_code')), 'empty_value' => $this->getObject()->get('eec_code'), 'required' => false)),
      'eec_desc' => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('job_category[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'JobCategory';
  }

}

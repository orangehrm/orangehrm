<?php

/**
 * CompanyProperty form base class.
 *
 * @method CompanyProperty getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseCompanyPropertyForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'prop_id'   => new sfWidgetFormInputHidden(),
      'prop_name' => new sfWidgetFormInputText(),
      'emp_id'    => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'prop_id'   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('prop_id')), 'empty_value' => $this->getObject()->get('prop_id'), 'required' => false)),
      'prop_name' => new sfValidatorString(array('max_length' => 250)),
      'emp_id'    => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('company_property[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CompanyProperty';
  }

}

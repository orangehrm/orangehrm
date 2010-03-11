<?php

/**
 * CompanyProperty form base class.
 *
 * @package    form
 * @subpackage company_property
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseCompanyPropertyForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'prop_id'   => new sfWidgetFormInputHidden(),
      'prop_name' => new sfWidgetFormInput(),
      'emp_id'    => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'prop_id'   => new sfValidatorDoctrineChoice(array('model' => 'CompanyProperty', 'column' => 'prop_id', 'required' => false)),
      'prop_name' => new sfValidatorString(array('max_length' => 250)),
      'emp_id'    => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('company_property[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CompanyProperty';
  }

}

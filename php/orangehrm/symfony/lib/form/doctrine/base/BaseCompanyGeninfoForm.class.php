<?php

/**
 * CompanyGeninfo form base class.
 *
 * @method CompanyGeninfo getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseCompanyGeninfoForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'code'           => new sfWidgetFormInputHidden(),
      'geninfo_keys'   => new sfWidgetFormInputText(),
      'geninfo_values' => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'code'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('code')), 'empty_value' => $this->getObject()->get('code'), 'required' => false)),
      'geninfo_keys'   => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'geninfo_values' => new sfValidatorString(array('max_length' => 800, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('company_geninfo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CompanyGeninfo';
  }

}

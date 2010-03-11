<?php

/**
 * CompanyGeninfo form base class.
 *
 * @package    form
 * @subpackage company_geninfo
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseCompanyGeninfoForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'code'           => new sfWidgetFormInputHidden(),
      'geninfo_keys'   => new sfWidgetFormInput(),
      'geninfo_values' => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'code'           => new sfValidatorDoctrineChoice(array('model' => 'CompanyGeninfo', 'column' => 'code', 'required' => false)),
      'geninfo_keys'   => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'geninfo_values' => new sfValidatorString(array('max_length' => 800, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('company_geninfo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CompanyGeninfo';
  }

}

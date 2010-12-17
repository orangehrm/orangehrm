<?php

/**
 * EmpLocations form base class.
 *
 * @method EmpLocations getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseEmpLocationsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'empNumber' => new sfWidgetFormInputHidden(),
      'loc_code'  => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'empNumber' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('empNumber')), 'empty_value' => $this->getObject()->get('empNumber'), 'required' => false)),
      'loc_code'  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('loc_code')), 'empty_value' => $this->getObject()->get('loc_code'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('emp_locations[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'EmpLocations';
  }

}

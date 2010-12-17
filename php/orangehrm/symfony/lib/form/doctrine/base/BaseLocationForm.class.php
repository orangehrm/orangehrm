<?php

/**
 * Location form base class.
 *
 * @method Location getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseLocationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'loc_code'       => new sfWidgetFormInputHidden(),
      'loc_name'       => new sfWidgetFormInputText(),
      'loc_country'    => new sfWidgetFormInputText(),
      'loc_state'      => new sfWidgetFormInputText(),
      'loc_city'       => new sfWidgetFormInputText(),
      'loc_add'        => new sfWidgetFormInputText(),
      'loc_zip'        => new sfWidgetFormInputText(),
      'loc_phone'      => new sfWidgetFormInputText(),
      'loc_fax'        => new sfWidgetFormInputText(),
      'loc_comments'   => new sfWidgetFormInputText(),
      'employees_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Employee')),
    ));

    $this->setValidators(array(
      'loc_code'       => new sfValidatorChoice(array('choices' => array($this->getObject()->get('loc_code')), 'empty_value' => $this->getObject()->get('loc_code'), 'required' => false)),
      'loc_name'       => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'loc_country'    => new sfValidatorString(array('max_length' => 3, 'required' => false)),
      'loc_state'      => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'loc_city'       => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'loc_add'        => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'loc_zip'        => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'loc_phone'      => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'loc_fax'        => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'loc_comments'   => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'employees_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Employee', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('location[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Location';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['employees_list']))
    {
      $this->setDefault('employees_list', $this->object->Employees->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveEmployeesList($con);

    parent::doSave($con);
  }

  public function saveEmployeesList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['employees_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Employees->getPrimaryKeys();
    $values = $this->getValue('employees_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Employees', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Employees', array_values($link));
    }
  }

}

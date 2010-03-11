<?php

/**
 * Location form base class.
 *
 * @package    form
 * @subpackage location
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseLocationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'loc_code'       => new sfWidgetFormInputHidden(),
      'loc_name'       => new sfWidgetFormInput(),
      'loc_country'    => new sfWidgetFormInput(),
      'loc_state'      => new sfWidgetFormInput(),
      'loc_city'       => new sfWidgetFormInput(),
      'loc_add'        => new sfWidgetFormInput(),
      'loc_zip'        => new sfWidgetFormInput(),
      'loc_phone'      => new sfWidgetFormInput(),
      'loc_fax'        => new sfWidgetFormInput(),
      'loc_comments'   => new sfWidgetFormInput(),
      'employees_list' => new sfWidgetFormDoctrineChoiceMany(array('model' => 'Employee')),
    ));

    $this->setValidators(array(
      'loc_code'       => new sfValidatorDoctrineChoice(array('model' => 'Location', 'column' => 'loc_code', 'required' => false)),
      'loc_name'       => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'loc_country'    => new sfValidatorString(array('max_length' => 3, 'required' => false)),
      'loc_state'      => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'loc_city'       => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'loc_add'        => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'loc_zip'        => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'loc_phone'      => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'loc_fax'        => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'loc_comments'   => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'employees_list' => new sfValidatorDoctrineChoiceMany(array('model' => 'Employee', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('location[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

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
    parent::doSave($con);

    $this->saveEmployeesList($con);
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

    if (is_null($con))
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

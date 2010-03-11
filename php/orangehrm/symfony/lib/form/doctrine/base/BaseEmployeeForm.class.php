<?php

/**
 * Employee form base class.
 *
 * @package    form
 * @subpackage employee
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseEmployeeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'empNumber'             => new sfWidgetFormInputHidden(),
      'lastName'              => new sfWidgetFormInput(),
      'firstName'             => new sfWidgetFormInput(),
      'middleName'            => new sfWidgetFormInput(),
      'nickName'              => new sfWidgetFormInput(),
      'smoker'                => new sfWidgetFormInput(),
      'ssn'                   => new sfWidgetFormInput(),
      'sin'                   => new sfWidgetFormInput(),
      'otherId'               => new sfWidgetFormInput(),
      'licenseNo'             => new sfWidgetFormInput(),
      'militaryService'       => new sfWidgetFormInput(),
      'street1'               => new sfWidgetFormInput(),
      'street2'               => new sfWidgetFormInput(),
      'city'                  => new sfWidgetFormInput(),
      'country'               => new sfWidgetFormInput(),
      'province'              => new sfWidgetFormInput(),
      'employeeId'            => new sfWidgetFormInput(),
      'ethnic_race_code'      => new sfWidgetFormDoctrineChoice(array('model' => 'EthnicRace', 'add_empty' => true)),
      'emp_birthday'          => new sfWidgetFormDate(),
      'nation_code'           => new sfWidgetFormDoctrineChoice(array('model' => 'Nationality', 'add_empty' => true)),
      'emp_gender'            => new sfWidgetFormInput(),
      'emp_marital_status'    => new sfWidgetFormInput(),
      'emp_dri_lice_exp_date' => new sfWidgetFormDate(),
      'emp_status'            => new sfWidgetFormDoctrineChoice(array('model' => 'EmployeeStatus', 'add_empty' => true)),
      'job_title_code'        => new sfWidgetFormDoctrineChoice(array('model' => 'JobTitle', 'add_empty' => true)),
      'eeo_cat_code'          => new sfWidgetFormDoctrineChoice(array('model' => 'JobCategory', 'add_empty' => true)),
      'work_station'          => new sfWidgetFormDoctrineChoice(array('model' => 'CompanyStructure', 'add_empty' => true)),
      'emp_zipcode'           => new sfWidgetFormInput(),
      'emp_hm_telephone'      => new sfWidgetFormInput(),
      'emp_mobile'            => new sfWidgetFormInput(),
      'emp_work_telephone'    => new sfWidgetFormInput(),
      'emp_work_email'        => new sfWidgetFormInput(),
      'sal_grd_code'          => new sfWidgetFormInput(),
      'joined_date'           => new sfWidgetFormDate(),
      'emp_oth_email'         => new sfWidgetFormInput(),
      'terminated_date'       => new sfWidgetFormDate(),
      'termination_reason'    => new sfWidgetFormTextarea(),
      'custom1'               => new sfWidgetFormInput(),
      'custom2'               => new sfWidgetFormInput(),
      'custom3'               => new sfWidgetFormInput(),
      'custom4'               => new sfWidgetFormInput(),
      'custom5'               => new sfWidgetFormInput(),
      'custom6'               => new sfWidgetFormInput(),
      'custom7'               => new sfWidgetFormInput(),
      'custom8'               => new sfWidgetFormInput(),
      'custom9'               => new sfWidgetFormInput(),
      'custom10'              => new sfWidgetFormInput(),
      'supervisors_list'      => new sfWidgetFormDoctrineChoiceMany(array('model' => 'Employee')),
      'locations_list'        => new sfWidgetFormDoctrineChoiceMany(array('model' => 'Location')),
      'subordinates_list'     => new sfWidgetFormDoctrineChoiceMany(array('model' => 'Employee')),
    ));

    $this->setValidators(array(
      'empNumber'             => new sfValidatorDoctrineChoice(array('model' => 'Employee', 'column' => 'emp_number', 'required' => false)),
      'lastName'              => new sfValidatorString(array('max_length' => 100)),
      'firstName'             => new sfValidatorString(array('max_length' => 100)),
      'middleName'            => new sfValidatorString(array('max_length' => 100)),
      'nickName'              => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'smoker'                => new sfValidatorInteger(array('required' => false)),
      'ssn'                   => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'sin'                   => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'otherId'               => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'licenseNo'             => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'militaryService'       => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'street1'               => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'street2'               => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'city'                  => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'country'               => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'province'              => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'employeeId'            => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'ethnic_race_code'      => new sfValidatorDoctrineChoice(array('model' => 'EthnicRace', 'required' => false)),
      'emp_birthday'          => new sfValidatorDate(array('required' => false)),
      'nation_code'           => new sfValidatorDoctrineChoice(array('model' => 'Nationality', 'required' => false)),
      'emp_gender'            => new sfValidatorInteger(array('required' => false)),
      'emp_marital_status'    => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'emp_dri_lice_exp_date' => new sfValidatorDate(array('required' => false)),
      'emp_status'            => new sfValidatorDoctrineChoice(array('model' => 'EmployeeStatus', 'required' => false)),
      'job_title_code'        => new sfValidatorDoctrineChoice(array('model' => 'JobTitle', 'required' => false)),
      'eeo_cat_code'          => new sfValidatorDoctrineChoice(array('model' => 'JobCategory', 'required' => false)),
      'work_station'          => new sfValidatorDoctrineChoice(array('model' => 'CompanyStructure', 'required' => false)),
      'emp_zipcode'           => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'emp_hm_telephone'      => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'emp_mobile'            => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'emp_work_telephone'    => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'emp_work_email'        => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'sal_grd_code'          => new sfValidatorString(array('max_length' => 13, 'required' => false)),
      'joined_date'           => new sfValidatorDate(array('required' => false)),
      'emp_oth_email'         => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'terminated_date'       => new sfValidatorDate(array('required' => false)),
      'termination_reason'    => new sfValidatorString(array('max_length' => 256, 'required' => false)),
      'custom1'               => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'custom2'               => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'custom3'               => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'custom4'               => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'custom5'               => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'custom6'               => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'custom7'               => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'custom8'               => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'custom9'               => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'custom10'              => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'supervisors_list'      => new sfValidatorDoctrineChoiceMany(array('model' => 'Employee', 'required' => false)),
      'locations_list'        => new sfValidatorDoctrineChoiceMany(array('model' => 'Location', 'required' => false)),
      'subordinates_list'     => new sfValidatorDoctrineChoiceMany(array('model' => 'Employee', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('employee[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Employee';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['supervisors_list']))
    {
      $this->setDefault('supervisors_list', $this->object->supervisors->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['locations_list']))
    {
      $this->setDefault('locations_list', $this->object->locations->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['subordinates_list']))
    {
      $this->setDefault('subordinates_list', $this->object->subordinates->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    parent::doSave($con);

    $this->savesupervisorsList($con);
    $this->savelocationsList($con);
    $this->savesubordinatesList($con);
  }

  public function savesupervisorsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['supervisors_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (is_null($con))
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->supervisors->getPrimaryKeys();
    $values = $this->getValue('supervisors_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('supervisors', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('supervisors', array_values($link));
    }
  }

  public function savelocationsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['locations_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (is_null($con))
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->locations->getPrimaryKeys();
    $values = $this->getValue('locations_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('locations', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('locations', array_values($link));
    }
  }

  public function savesubordinatesList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['subordinates_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (is_null($con))
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->subordinates->getPrimaryKeys();
    $values = $this->getValue('subordinates_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('subordinates', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('subordinates', array_values($link));
    }
  }

}

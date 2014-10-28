<?php

/**
 * Employee form base class.
 *
 * @method Employee getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseEmployeeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'empNumber'             => new sfWidgetFormInputHidden(),
      'lastName'              => new sfWidgetFormInputText(),
      'firstName'             => new sfWidgetFormInputText(),
      'middleName'            => new sfWidgetFormInputText(),
      'nickName'              => new sfWidgetFormInputText(),
      'smoker'                => new sfWidgetFormInputText(),
      'ssn'                   => new sfWidgetFormInputText(),
      'sin'                   => new sfWidgetFormInputText(),
      'otherId'               => new sfWidgetFormInputText(),
      'licenseNo'             => new sfWidgetFormInputText(),
      'militaryService'       => new sfWidgetFormInputText(),
      'street1'               => new sfWidgetFormInputText(),
      'street2'               => new sfWidgetFormInputText(),
      'city'                  => new sfWidgetFormInputText(),
      'country'               => new sfWidgetFormInputText(),
      'province'              => new sfWidgetFormInputText(),
      'employeeId'            => new sfWidgetFormInputText(),
      'ethnic_race_code'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('EthnicRace'), 'add_empty' => true)),
      'emp_birthday'          => new sfWidgetFormDate(),
      'nation_code'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Nationality'), 'add_empty' => true)),
      'emp_gender'            => new sfWidgetFormInputText(),
      'emp_marital_status'    => new sfWidgetFormInputText(),
      'emp_dri_lice_exp_date' => new sfWidgetFormDate(),
      'emp_status'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('employeeStatus'), 'add_empty' => true)),
      'job_title_code'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('jobTitle'), 'add_empty' => true)),
      'eeo_cat_code'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('JobCategory'), 'add_empty' => true)),
      'work_station'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('subDivision'), 'add_empty' => true)),
      'emp_zipcode'           => new sfWidgetFormInputText(),
      'emp_hm_telephone'      => new sfWidgetFormInputText(),
      'emp_mobile'            => new sfWidgetFormInputText(),
      'emp_work_telephone'    => new sfWidgetFormInputText(),
      'emp_work_email'        => new sfWidgetFormInputText(),
      'sal_grd_code'          => new sfWidgetFormInputText(),
      'joined_date'           => new sfWidgetFormDate(),
      'emp_oth_email'         => new sfWidgetFormInputText(),
      'terminated_date'       => new sfWidgetFormDate(),
      'termination_reason'    => new sfWidgetFormTextarea(),
      'custom1'               => new sfWidgetFormInputText(),
      'custom2'               => new sfWidgetFormInputText(),
      'custom3'               => new sfWidgetFormInputText(),
      'custom4'               => new sfWidgetFormInputText(),
      'custom5'               => new sfWidgetFormInputText(),
      'custom6'               => new sfWidgetFormInputText(),
      'custom7'               => new sfWidgetFormInputText(),
      'custom8'               => new sfWidgetFormInputText(),
      'custom9'               => new sfWidgetFormInputText(),
      'custom10'              => new sfWidgetFormInputText(),
      'supervisors_list'      => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Employee')),
      'locations_list'        => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Location')),
      'subordinates_list'     => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Employee')),
    ));

    $this->setValidators(array(
      'empNumber'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('empNumber')), 'empty_value' => $this->getObject()->get('empNumber'), 'required' => false)),
      'lastName'              => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'firstName'             => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'middleName'            => new sfValidatorString(array('max_length' => 100, 'required' => false)),
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
      'ethnic_race_code'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('EthnicRace'), 'required' => false)),
      'emp_birthday'          => new sfValidatorDate(array('required' => false)),
      'nation_code'           => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Nationality'), 'required' => false)),
      'emp_gender'            => new sfValidatorInteger(array('required' => false)),
      'emp_marital_status'    => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'emp_dri_lice_exp_date' => new sfValidatorDate(array('required' => false)),
      'emp_status'            => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('employeeStatus'), 'required' => false)),
      'job_title_code'        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('jobTitle'), 'required' => false)),
      'eeo_cat_code'          => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('JobCategory'), 'required' => false)),
      'work_station'          => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('subDivision'), 'required' => false)),
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
      'supervisors_list'      => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Employee', 'required' => false)),
      'locations_list'        => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Location', 'required' => false)),
      'subordinates_list'     => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Employee', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('employee[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

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
    $this->savesupervisorsList($con);
    $this->savelocationsList($con);
    $this->savesubordinatesList($con);

    parent::doSave($con);
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

    if (null === $con)
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

    if (null === $con)
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

    if (null === $con)
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

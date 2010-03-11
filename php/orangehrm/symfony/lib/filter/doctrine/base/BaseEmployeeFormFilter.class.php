<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/doctrine/BaseFormFilterDoctrine.class.php');

/**
 * Employee filter form base class.
 *
 * @package    filters
 * @subpackage Employee *
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class BaseEmployeeFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'lastName'              => new sfWidgetFormFilterInput(),
      'firstName'             => new sfWidgetFormFilterInput(),
      'middleName'            => new sfWidgetFormFilterInput(),
      'nickName'              => new sfWidgetFormFilterInput(),
      'smoker'                => new sfWidgetFormFilterInput(),
      'ssn'                   => new sfWidgetFormFilterInput(),
      'sin'                   => new sfWidgetFormFilterInput(),
      'otherId'               => new sfWidgetFormFilterInput(),
      'licenseNo'             => new sfWidgetFormFilterInput(),
      'militaryService'       => new sfWidgetFormFilterInput(),
      'street1'               => new sfWidgetFormFilterInput(),
      'street2'               => new sfWidgetFormFilterInput(),
      'city'                  => new sfWidgetFormFilterInput(),
      'country'               => new sfWidgetFormFilterInput(),
      'province'              => new sfWidgetFormFilterInput(),
      'employeeId'            => new sfWidgetFormFilterInput(),
      'ethnic_race_code'      => new sfWidgetFormFilterInput(),
      'emp_birthday'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'nation_code'           => new sfWidgetFormFilterInput(),
      'emp_gender'            => new sfWidgetFormFilterInput(),
      'emp_marital_status'    => new sfWidgetFormFilterInput(),
      'emp_dri_lice_exp_date' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'emp_status'            => new sfWidgetFormDoctrineChoice(array('model' => 'EmployeeStatus', 'add_empty' => true)),
      'job_title_code'        => new sfWidgetFormDoctrineChoice(array('model' => 'JobTitle', 'add_empty' => true)),
      'eeo_cat_code'          => new sfWidgetFormFilterInput(),
      'work_station'          => new sfWidgetFormDoctrineChoice(array('model' => 'CompanyStructure', 'add_empty' => true)),
      'emp_zipcode'           => new sfWidgetFormFilterInput(),
      'emp_hm_telephone'      => new sfWidgetFormFilterInput(),
      'emp_mobile'            => new sfWidgetFormFilterInput(),
      'emp_work_telephone'    => new sfWidgetFormFilterInput(),
      'emp_work_email'        => new sfWidgetFormFilterInput(),
      'sal_grd_code'          => new sfWidgetFormFilterInput(),
      'joined_date'           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'emp_oth_email'         => new sfWidgetFormFilterInput(),
      'terminated_date'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'termination_reason'    => new sfWidgetFormFilterInput(),
      'custom1'               => new sfWidgetFormFilterInput(),
      'custom2'               => new sfWidgetFormFilterInput(),
      'custom3'               => new sfWidgetFormFilterInput(),
      'custom4'               => new sfWidgetFormFilterInput(),
      'custom5'               => new sfWidgetFormFilterInput(),
      'custom6'               => new sfWidgetFormFilterInput(),
      'custom7'               => new sfWidgetFormFilterInput(),
      'custom8'               => new sfWidgetFormFilterInput(),
      'custom9'               => new sfWidgetFormFilterInput(),
      'custom10'              => new sfWidgetFormFilterInput(),
      'supervisors_list'      => new sfWidgetFormDoctrineChoiceMany(array('model' => 'Employee')),
      'subordinates_list'     => new sfWidgetFormDoctrineChoiceMany(array('model' => 'Employee')),
    ));

    $this->setValidators(array(
      'lastName'              => new sfValidatorPass(array('required' => false)),
      'firstName'             => new sfValidatorPass(array('required' => false)),
      'middleName'            => new sfValidatorPass(array('required' => false)),
      'nickName'              => new sfValidatorPass(array('required' => false)),
      'smoker'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ssn'                   => new sfValidatorPass(array('required' => false)),
      'sin'                   => new sfValidatorPass(array('required' => false)),
      'otherId'               => new sfValidatorPass(array('required' => false)),
      'licenseNo'             => new sfValidatorPass(array('required' => false)),
      'militaryService'       => new sfValidatorPass(array('required' => false)),
      'street1'               => new sfValidatorPass(array('required' => false)),
      'street2'               => new sfValidatorPass(array('required' => false)),
      'city'                  => new sfValidatorPass(array('required' => false)),
      'country'               => new sfValidatorPass(array('required' => false)),
      'province'              => new sfValidatorPass(array('required' => false)),
      'employeeId'            => new sfValidatorPass(array('required' => false)),
      'ethnic_race_code'      => new sfValidatorPass(array('required' => false)),
      'emp_birthday'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'nation_code'           => new sfValidatorPass(array('required' => false)),
      'emp_gender'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'emp_marital_status'    => new sfValidatorPass(array('required' => false)),
      'emp_dri_lice_exp_date' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'emp_status'            => new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'EmployeeStatus', 'column' => 'id')),
      'job_title_code'        => new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'JobTitle', 'column' => 'id')),
      'eeo_cat_code'          => new sfValidatorPass(array('required' => false)),
      'work_station'          => new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'CompanyStructure', 'column' => 'id')),
      'emp_zipcode'           => new sfValidatorPass(array('required' => false)),
      'emp_hm_telephone'      => new sfValidatorPass(array('required' => false)),
      'emp_mobile'            => new sfValidatorPass(array('required' => false)),
      'emp_work_telephone'    => new sfValidatorPass(array('required' => false)),
      'emp_work_email'        => new sfValidatorPass(array('required' => false)),
      'sal_grd_code'          => new sfValidatorPass(array('required' => false)),
      'joined_date'           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'emp_oth_email'         => new sfValidatorPass(array('required' => false)),
      'terminated_date'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'termination_reason'    => new sfValidatorPass(array('required' => false)),
      'custom1'               => new sfValidatorPass(array('required' => false)),
      'custom2'               => new sfValidatorPass(array('required' => false)),
      'custom3'               => new sfValidatorPass(array('required' => false)),
      'custom4'               => new sfValidatorPass(array('required' => false)),
      'custom5'               => new sfValidatorPass(array('required' => false)),
      'custom6'               => new sfValidatorPass(array('required' => false)),
      'custom7'               => new sfValidatorPass(array('required' => false)),
      'custom8'               => new sfValidatorPass(array('required' => false)),
      'custom9'               => new sfValidatorPass(array('required' => false)),
      'custom10'              => new sfValidatorPass(array('required' => false)),
      'supervisors_list'      => new sfValidatorDoctrineChoiceMany(array('model' => 'Employee', 'required' => false)),
      'subordinates_list'     => new sfValidatorDoctrineChoiceMany(array('model' => 'Employee', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('employee_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function addSupervisorsListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query->leftJoin('r.ReportTo ReportTo')
          ->andWhereIn('ReportTo.erep_sup_emp_number', $values);
  }

  public function addSubordinatesListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query->leftJoin('r.ReportTo ReportTo')
          ->andWhereIn('ReportTo.erep_sub_emp_number', $values);
  }

  public function getModelName()
  {
    return 'Employee';
  }

  public function getFields()
  {
    return array(
      'empNumber'             => 'Number',
      'lastName'              => 'Text',
      'firstName'             => 'Text',
      'middleName'            => 'Text',
      'nickName'              => 'Text',
      'smoker'                => 'Number',
      'ssn'                   => 'Text',
      'sin'                   => 'Text',
      'otherId'               => 'Text',
      'licenseNo'             => 'Text',
      'militaryService'       => 'Text',
      'street1'               => 'Text',
      'street2'               => 'Text',
      'city'                  => 'Text',
      'country'               => 'Text',
      'province'              => 'Text',
      'employeeId'            => 'Text',
      'ethnic_race_code'      => 'Text',
      'emp_birthday'          => 'Date',
      'nation_code'           => 'Text',
      'emp_gender'            => 'Number',
      'emp_marital_status'    => 'Text',
      'emp_dri_lice_exp_date' => 'Date',
      'emp_status'            => 'ForeignKey',
      'job_title_code'        => 'ForeignKey',
      'eeo_cat_code'          => 'Text',
      'work_station'          => 'ForeignKey',
      'emp_zipcode'           => 'Text',
      'emp_hm_telephone'      => 'Text',
      'emp_mobile'            => 'Text',
      'emp_work_telephone'    => 'Text',
      'emp_work_email'        => 'Text',
      'sal_grd_code'          => 'Text',
      'joined_date'           => 'Date',
      'emp_oth_email'         => 'Text',
      'terminated_date'       => 'Date',
      'termination_reason'    => 'Text',
      'custom1'               => 'Text',
      'custom2'               => 'Text',
      'custom3'               => 'Text',
      'custom4'               => 'Text',
      'custom5'               => 'Text',
      'custom6'               => 'Text',
      'custom7'               => 'Text',
      'custom8'               => 'Text',
      'custom9'               => 'Text',
      'custom10'              => 'Text',
      'supervisors_list'      => 'ManyKey',
      'subordinates_list'     => 'ManyKey',
    );
  }
}
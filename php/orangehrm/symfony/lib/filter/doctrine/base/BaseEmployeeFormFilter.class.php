<?php

/**
 * Employee filter form base class.
 *
 * @package    orangehrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseEmployeeFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'lastName'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'firstName'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'middleName'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
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
      'ethnic_race_code'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('EthnicRace'), 'add_empty' => true)),
      'emp_birthday'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'nation_code'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Nationality'), 'add_empty' => true)),
      'emp_gender'            => new sfWidgetFormFilterInput(),
      'emp_marital_status'    => new sfWidgetFormFilterInput(),
      'emp_dri_lice_exp_date' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'emp_status'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('employeeStatus'), 'add_empty' => true)),
      'job_title_code'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('jobTitle'), 'add_empty' => true)),
      'eeo_cat_code'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('JobCategory'), 'add_empty' => true)),
      'work_station'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('subDivision'), 'add_empty' => true)),
      'emp_zipcode'           => new sfWidgetFormFilterInput(),
      'emp_hm_telephone'      => new sfWidgetFormFilterInput(),
      'emp_mobile'            => new sfWidgetFormFilterInput(),
      'emp_work_telephone'    => new sfWidgetFormFilterInput(),
      'emp_work_email'        => new sfWidgetFormFilterInput(),
      'sal_grd_code'          => new sfWidgetFormFilterInput(),
      'joined_date'           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'emp_oth_email'         => new sfWidgetFormFilterInput(),
      'terminated_date'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
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
      'supervisors_list'      => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Employee')),
      'locations_list'        => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Location')),
      'subordinates_list'     => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Employee')),
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
      'ethnic_race_code'      => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('EthnicRace'), 'column' => 'ethnic_race_code')),
      'emp_birthday'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'nation_code'           => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Nationality'), 'column' => 'nat_code')),
      'emp_gender'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'emp_marital_status'    => new sfValidatorPass(array('required' => false)),
      'emp_dri_lice_exp_date' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'emp_status'            => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('employeeStatus'), 'column' => 'id')),
      'job_title_code'        => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('jobTitle'), 'column' => 'id')),
      'eeo_cat_code'          => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('JobCategory'), 'column' => 'eec_code')),
      'work_station'          => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('subDivision'), 'column' => 'id')),
      'emp_zipcode'           => new sfValidatorPass(array('required' => false)),
      'emp_hm_telephone'      => new sfValidatorPass(array('required' => false)),
      'emp_mobile'            => new sfValidatorPass(array('required' => false)),
      'emp_work_telephone'    => new sfValidatorPass(array('required' => false)),
      'emp_work_email'        => new sfValidatorPass(array('required' => false)),
      'sal_grd_code'          => new sfValidatorPass(array('required' => false)),
      'joined_date'           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'emp_oth_email'         => new sfValidatorPass(array('required' => false)),
      'terminated_date'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
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
      'supervisors_list'      => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Employee', 'required' => false)),
      'locations_list'        => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Location', 'required' => false)),
      'subordinates_list'     => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Employee', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('employee_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

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

    $query
      ->leftJoin($query->getRootAlias().'.ReportTo ReportTo')
      ->andWhereIn('ReportTo.erep_sup_emp_number', $values)
    ;
  }

  public function addLocationsListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.EmpLocations EmpLocations')
      ->andWhereIn('EmpLocations.loc_code', $values)
    ;
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

    $query
      ->leftJoin($query->getRootAlias().'.ReportTo ReportTo')
      ->andWhereIn('ReportTo.erep_sub_emp_number', $values)
    ;
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
      'ethnic_race_code'      => 'ForeignKey',
      'emp_birthday'          => 'Date',
      'nation_code'           => 'ForeignKey',
      'emp_gender'            => 'Number',
      'emp_marital_status'    => 'Text',
      'emp_dri_lice_exp_date' => 'Date',
      'emp_status'            => 'ForeignKey',
      'job_title_code'        => 'ForeignKey',
      'eeo_cat_code'          => 'ForeignKey',
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
      'locations_list'        => 'ManyKey',
      'subordinates_list'     => 'ManyKey',
    );
  }
}

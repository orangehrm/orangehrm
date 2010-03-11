<?php

/**
 * ReportTo form base class.
 *
 * @package    form
 * @subpackage report_to
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseReportToForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'supervisorId'  => new sfWidgetFormInputHidden(),
      'subordinateId' => new sfWidgetFormInputHidden(),
      'reportingMode' => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'supervisorId'  => new sfValidatorDoctrineChoice(array('model' => 'ReportTo', 'column' => 'erep_sup_emp_number', 'required' => false)),
      'subordinateId' => new sfValidatorDoctrineChoice(array('model' => 'ReportTo', 'column' => 'erep_sub_emp_number', 'required' => false)),
      'reportingMode' => new sfValidatorDoctrineChoice(array('model' => 'ReportTo', 'column' => 'erep_reporting_mode', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('report_to[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ReportTo';
  }

}

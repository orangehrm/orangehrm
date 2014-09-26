<?php

/**
 * ReportTo form base class.
 *
 * @method ReportTo getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseReportToForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'supervisorId'  => new sfWidgetFormInputHidden(),
      'subordinateId' => new sfWidgetFormInputHidden(),
      'reportingMode' => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'supervisorId'  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('supervisorId')), 'empty_value' => $this->getObject()->get('supervisorId'), 'required' => false)),
      'subordinateId' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('subordinateId')), 'empty_value' => $this->getObject()->get('subordinateId'), 'required' => false)),
      'reportingMode' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('reportingMode')), 'empty_value' => $this->getObject()->get('reportingMode'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('report_to[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ReportTo';
  }

}

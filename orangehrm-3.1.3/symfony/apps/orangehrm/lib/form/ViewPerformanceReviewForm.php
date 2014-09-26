<?php

/*
  // OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
  // all the essential functionalities required for any enterprise.
  // Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com

  // OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
  // the GNU General Public License as published by the Free Software Foundation; either
  // version 2 of the License, or (at your option) any later version.

  // OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
  // without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
  // See the GNU General Public License for more details.

  // You should have received a copy of the GNU General Public License along with this program;
  // if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
  // Boston, MA  02110-1301, USA
 */

/**
 * Form class for Performance reviews Admin/Reviewer/Employee
 */
class ViewPerformanceReviewForm extends BaseForm {

    private $companyStructureService;

    public function getCompanyStructureService() {
        if (is_null($this->companyStructureService)) {
            $this->companyStructureService = new CompanyStructureService();
            $this->companyStructureService->setCompanyStructureDao(new CompanyStructureDao());
        }
        return $this->companyStructureService;
    }

    public function setCompanyStructureService(CompanyStructureService $companyStructureService) {
        $this->companyStructureService = $companyStructureService;
    }

    public function configure() {

        $this->setWidgets(array(
            'ReviewPeriodFrom' => new sfWidgetFormInputText(),
            'ReviewPeriodTo' => new sfWidgetFormInputText(),
            'JobTitle' => new sfWidgetFormDoctrineChoice(array('model' => 'JobTitle', 'add_empty' => '- Select -')),
            'SubUnit' => new sfWidgetFormChoice(array('choices' => $this->__getSubunitList())),
            'Employee' => new sfWidgetFormInputText(array(), array('onkeyup' => 'lookup(this.value);', 'onblur' => 'fill();')),
            'Reviewer' => new sfWidgetFormInputText(),
        ));

        $this->widgetSchema->setNameFormat('viewreview[%s]');

        $this->setValidators(array(
            'ReviewPeriodFrom' => new sfValidatorDate(array('required' => false)),
            'ReviewPeriodTo' => new sfValidatorDate(array('required' => false)),
            'JobTitle' => new sfValidatorDoctrineChoice(array('model' => 'JobTitle', 'column' => 'jobtit_code ', 'required' => false)),
            'SubUnit' => new sfValidatorChoice(array('choices' => array_keys($this->__getSubunitList()) , 'required' => false)),
            'Employee' => new sfValidatorNumber(array('required' => false)),
            'Reviewer' => new sfValidatorString(array('required' => false)),
        ));
        /* $this->validatorSchema->setPostValidator(
          new sfValidatorCallback(array('callback' => array($this, 'checkMinMaxRates')))
          ); */
    }

    private function __getSubunitList() {
        $subUnitList = array(0 => __("All"));
        $treeObject = $this->getCompanyStructureService()->getSubunitTreeObject();

        $tree = $treeObject->fetchTree();

        foreach ($tree as $node) {
            if ($node->getId() != 1) {
                $subUnitList[$node->getId()] = str_repeat('&nbsp;&nbsp;', $node['level'] - 1) . $node['name'];
            }
        }
        return $subUnitList;
    }

    /**
     * check if the minimum rate is higher than the maximum value
     * @param $validator
     * @param $values
     * @return array
     */
    /* public function checkMinMaxRates($validator, $values){
      if (($values['MinRate'] >= $values['MaxRate']) && ($values['MinRate'] && $values['MaxRate'])){
      throw new sfValidatorError($validator, 'Minimum Value is higher than Maximum value. Please correct the values properly.');
      } else if(($values['MinRate'] == "") && ($values['MaxRate'] != "")) {
      throw new sfValidatorError($validator, 'Minimum value is not entered.');
      } else if(($values['MaxRate'] == "") && ($values['MinRate'] != "")) {
      throw new sfValidatorError($validator, 'Maximum value is not entered.');
      } else {
      return $values;
      }
      } */

    private function _getAllJobTitles() {
        $jobTitle = new JobTitle();

        $kpiDefinedJobTitles = $jobTitle->getJobTitlesDefined();

        if (empty($kpiDefinedJobTitles)) {
            $choices = array('-1' => '- Select -');
        } else {
            foreach ($kpiDefinedJobTitles as $key => $val) {
                foreach ($val as $jobTitleId => $jobTitleName) {
                    $arrFinal[$jobTitleId] = $jobTitleName;
                }
            }
            $choices = array('-1' => '- Select -') + $arrFinal;
        }
        return $choices;
    }

}


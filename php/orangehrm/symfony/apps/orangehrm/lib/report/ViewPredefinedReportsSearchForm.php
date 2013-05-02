<?php

class ViewPredefinedReportsSearchForm extends sfForm {

    public function configure() {

        $this->setWidgets(array(
            'search' => new sfWidgetFormInputText()
        ));

        $this->widgetSchema->setNameFormat('search[%s]');

        $this->setValidators(array(
            'search' => new sfValidatorString(array('required' => false))
        ));
        
        $this->widgetSchema->setLabels(array(
            'search' => __('Report Name')
        ));
    }

    public function getReportListAsJson($reportList) {

        $jsonArray = array();

        $reportArray = array();
        foreach ($reportList as $report) {
            $name = $report->getName();
            $reportArray[$report->getReportId()] = $name;
            $jsonArray[] = array('name' => $name, 'id' => $report->getReportId());
        }

        $jsonString = json_encode($jsonArray);
        return $jsonString;
    }

}
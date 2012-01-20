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
    }

    public function getReportListAsJson($reportList) {

        $jsonArray = array();
        $escapeCharSet = array(38, 39, 34, 60, 61, 62, 63, 64, 58, 59, 94, 96);

        $reportArray = array();
        foreach ($reportList as $report) {
            $name = $report->getName();

            foreach ($escapeCharSet as $char) {
                $name = str_replace(chr($char), (chr(92) . chr($char)), $name);
            }

            $reportArray[$report->getReportId()] = $name;
            $jsonArray[] = array('name' => $name, 'id' => $report->getReportId());
        }

        $jsonString = json_encode($jsonArray);
        return $jsonString;
    }

}


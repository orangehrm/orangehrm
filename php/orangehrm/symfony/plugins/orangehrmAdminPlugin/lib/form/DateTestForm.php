<?php

class DateTestForm extends BaseForm {

    public function configure() {
        $this->setWidgets(array(
            'fromDate' => new ohrmWidgetDatePickerNew(array(), array('id' => 'from_date')),
            'toDate' => new ohrmWidgetDatePickerNew(array(), array('id' => 'to_date')),
        ));

        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();

        //Setting validators
        $this->setValidators(array(
            'fromDate' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false),
                    array('invalid' => 'Date format should be ' . $inputDatePattern)),
            'toDate' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false),
                    array('invalid' => 'Date format should be ' . $inputDatePattern)),
        ));
        $this->widgetSchema->setNameFormat('dateTest[%s]');
        $date = "2011-10-25";
        $dateArray = explode('-', $date);
        $dateTime = new DateTime();
        $dateTime->setDate($dateArray[0], $dateArray[1], $dateArray[2]);
        $date = $dateTime->format($inputDatePattern);
//        $this->setDefault('fromDate', date($inputDatePattern));
//        $this->setDefault('fromDate', $date);
    }

}

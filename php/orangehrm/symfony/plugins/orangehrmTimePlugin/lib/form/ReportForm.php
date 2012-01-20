<?php

class ReportForm extends sfForm {

    public function configure() {

        $this->setWidgets(array());

        $this->widgetSchema->setNameFormat('time[%s]');
        
    }

}


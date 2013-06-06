<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TimesheetFormToImplementCsrfTokens
 *
 * @author orangehrm
 */
class TimesheetFormToImplementCsrfTokens extends sfForm {

    public function configure() {

        $this->setWidgets(array());

        $this->widgetSchema->setNameFormat('time[%s]');
    }

}

?>

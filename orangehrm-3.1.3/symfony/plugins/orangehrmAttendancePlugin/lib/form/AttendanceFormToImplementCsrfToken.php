<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AttendanceFormToImplementCsrfToken
 *
 * @author orangehrm
 */
class AttendanceFormToImplementCsrfToken extends sfForm {

    public function configure() {

        $this->setWidgets(array());

        $this->widgetSchema->setNameFormat('attendance[%s]');
    }
    
    }

?>

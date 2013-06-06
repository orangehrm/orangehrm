<?php

class orangehrmForm extends sfForm {

    private $messageDiv;

    public function setMessage($messageType, $message) {

        $messageType = strtolower($messageType);

        if ($messageType == 'success') {

            $div = "<div id=\"messageDiv\" class=\"messageBalloon_success\">\n";
            $div .= "$message\n";
            $div .= "</div>\n";

            $this->messageDiv = $div;

        }

    }

    public function getMessage() {

        if (empty($this->messageDiv)) {

            $div = "<div id=\"messageDiv\">\n";
            $div .= "</div>\n";

            $this->messageDiv = $div;

        }

        return $this->messageDiv;

    }



}


?>

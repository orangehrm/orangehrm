<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

/**
 * Description of messageBoxComponent
 *
 * @author aruna
 */
class messageBoxComponent extends sfComponent {

    const SUCCESS_HEADING = "Success";
    const ERROR_HEADING = "Error!";
    const CONFIRM_HEADING = "Confirm";
    const DELETE_MESSAGE_TYPE = "delete";
    const DELETE_MESSAGE_BODY = "Do you really want to delete this?";
    const HIDDEN_ELEMENT_CLASS = "hidden-element";
    const DELETE_CONFIRM_BTN_ID = "delete_confirm";
    const DELETE_DISCARD_BTN_ID = "delete_discard";

    public function execute($request) {
        
        switch ($this->messageType){
            case messageBoxComponent::DELETE_MESSAGE_TYPE:
                $this->messageHeading = messageBoxComponent::CONFIRM_HEADING;
                $this->messageBody = messageBoxComponent::DELETE_MESSAGE_BODY;
                $this->okBtnClass = messageBoxComponent::HIDDEN_ELEMENT_CLASS;
                $this->yesBtnId = messageBoxComponent::DELETE_CONFIRM_BTN_ID;
                $this->noBtnId = messageBoxComponent::DELETE_DISCARD_BTN_ID;
                break;
        }
        
    }

}

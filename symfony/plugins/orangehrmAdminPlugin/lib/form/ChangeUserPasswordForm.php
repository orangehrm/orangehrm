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
 *
 */
class ChangeUserPasswordForm extends BaseForm {

    public function configure() {

        $this->setWidgets(array(
            'userId' => new sfWidgetFormInputHidden(),
            'currentPassword' => new sfWidgetFormInputPassword(array(), array("class" => "formInputText", "maxlength" => 20)),
            'newPassword' => new sfWidgetFormInputPassword(array(), array("class" => "formInputText", "maxlength" => 20)),
            'confirmNewPassword' => new sfWidgetFormInputPassword(array(), array("class" => "formInputText", "maxlength" => 20))
        ));

        $this->setValidators(array(
            'userId' => new sfValidatorNumber(array('required' => false)),
            'currentPassword' => new sfValidatorString(array('required' => true, 'max_length' => 20)),
            'newPassword' => new sfValidatorString(array('required' => true, 'max_length' => 20)),
            'confirmNewPassword' => new sfValidatorString(array('required' => true, 'max_length' => 20))
        ));

        
        $this->widgetSchema->setNameFormat('changeUserPassword[%s]');

        $this->getWidgetSchema()->setLabels($this->getFormLabels());

        //merge secondary password
        $formExtension = PluginFormMergeManager::instance();
        $formExtension->mergeForms($this, 'changeUserPassword', 'ChangeUserPasswordForm');

    }

    /**
     *
     * @return array
     */
    protected function getFormLabels() {
        $requiredMarker = ' <em> *</em>';
        $labels = array(
            'userId' => false,
            'currentPassword' => __('Current Password')  . $requiredMarker,
            'newPassword' => __('New Password') . $requiredMarker,
            'confirmNewPassword' => __('Confirm New Password') . $requiredMarker,
            'currentPassword' => __('Current Password') . $requiredMarker,
        );

        return $labels;
    }

    public function save() {

        $userId = sfContext::getInstance()->getUser()->getAttribute('user')->getUserId();
        $systemUserService = new SystemUserService();
        $posts = $this->getValues();
        $systemUserService->updatePassword($userId, $posts['newPassword']);

        //save secondary password
        $formExtension = PluginFormMergeManager::instance();
        $formExtension->saveMergeForms($this, 'changeUserPassword', 'ChangeUserPasswordForm');
    }

}

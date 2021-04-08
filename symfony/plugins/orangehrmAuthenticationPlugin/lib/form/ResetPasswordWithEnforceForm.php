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
class ResetPasswordWithEnforceForm extends sfForm
{

    public $formWidgets = array();
    public $formValidators = array();

    public function configure()
    {
        $this->formWidgets['currentPassword'] = new sfWidgetFormInputPassword(array(), array("autocomplete" => "off"));
        $this->formWidgets['newPassword'] = new ohrmWidgetFormInputPassword(array(), array("autocomplete" => "off"));
        $this->formWidgets['passwordConfirmation'] = new sfWidgetFormInputPassword(array(), array("autocomplete" => "off"));

        $this->setWidgets($this->formWidgets);

        $this->formValidators['currentPassword'] = new sfValidatorString(array('required' => false));
        $this->formValidators['newPassword'] = new ohrmValidatorPassword(array('required' => false,  'max_length' => 64));
        $this->formValidators['passwordConfirmation'] = new sfValidatorPassword(array('required' => false,'min_length' => 8, 'max_length' => 64, 'trim' => true));
        $this->widgetSchema->setNameFormat('changeWeakPassword[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());
        $this->setValidators($this->formValidators);
    }

    /**
     *
     * @return array
     */
    protected function getFormLabels()
    {
        $requiredMarker = ' <em> *</em>';
        $labels = array(
            'userId' => false,
            'currentPassword' => __('Current Password') . $requiredMarker,
            'newPassword' => __('New Password') . $requiredMarker,
            'passwordConfirmation' => __('Confirm New Password') . $requiredMarker,
        );

        return $labels;
    }

}


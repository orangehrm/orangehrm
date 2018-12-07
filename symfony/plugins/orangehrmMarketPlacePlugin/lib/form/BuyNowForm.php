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
 * Boston, MA 02110-1301, USA
 */

/**
 * Class BuyNowForm
 */
class BuyNowForm extends BaseForm
{
    /**
     * @configure function of form
     */
    public function configure()
    {
        $this->setWidgets($this->getWidgetList());
        $this->setValidators($this->getValidatorList());
        $this->getWidgetSchema()->setLabels($this->getLabelList());
    }

    /**
     * @return array
     */
    public function getWidgetList()
    {
        $widgets = array();
        $widgets['email'] = new sfWidgetFormInputText();
        $widgets['contactNumber'] = new sfWidgetFormInputText();
        $widgets['organization'] = new sfWidgetFormInputText();
        return $widgets;
    }

    /**
     * @return array
     */
    public function getValidatorList()
    {
        $validators = array();
        $validators['email'] = new sfValidatorEmail(array('required' => true));
        $validators['contactNumber'] = new sfValidatorNumber(array('required' => true));
        $validators['organization'] = new sfValidatorString(array('required' => true));
        return $validators;
    }

    /**
     * @return array
     */
    public function getLabelList()
    {
        $requiredMarker = ' <em>*</em>';
        $lableList = array();
        $lableList['email'] = __('Email') . $requiredMarker;
        $lableList['contactNumber'] = __('Contact Number') . $requiredMarker;
        $lableList['organization'] = __('Organization') . $requiredMarker;
        return $lableList;
    }
}

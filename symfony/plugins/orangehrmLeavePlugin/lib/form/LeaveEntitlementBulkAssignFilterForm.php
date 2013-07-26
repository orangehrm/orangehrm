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
 * Description of LeaveEntitlementBulkAssignFilterForm
 *
 */
class LeaveEntitlementBulkAssignFilterForm extends BaseForm {

    public function configure() {
        
        $labels = array();

        // Add employee filter checkbox and widgets
        $this->setWidget('bulk_assign', new sfWidgetFormInputCheckbox());
        //$this->setValidator('bulk_assign', new sfValidatorChoice(array('choices' => array(1), 'required' => false)));
        $this->setValidator('bulk_assign', new sfValidatorPass());
        $labels['bulk_assign'] = __('Add to Multiple Employees');
        
        $locationOptions = array('set_all_option_value' => true, 'all_option_value' => NULL, 'show_all_locations' => false);                
        $locationWidget = new ohrmReportWidgetOperationalCountryLocationDropDown($locationOptions);
        $this->setWidget('location', $locationWidget);
        $labels['location'] = __('Location');
        //$locationChoices = $locationWidget->getValidValues();
        //$this->setValidator('location', new sfValidatorChoice(array('choices' => $locationChoices, 'required' => false)));
        $this->setValidator('location', new sfValidatorPass());

        $subUnitWidget = new ohrmWidgetSubUnitDropDown();
        $this->setWidget('subunit', $subUnitWidget);
        $labels['subunit'] = __('Sub Unit');
        $subUnitChoices = $subUnitWidget->getValidValues();
        $this->setValidator('subunit', new sfValidatorChoice(array('choices' => $subUnitChoices, 'required' => false)));

        $this->getWidgetSchema()->setLabels($labels);

        $formExtension = PluginFormMergeManager::instance();
        $formExtension->mergeForms($this, 'viewLeaveEntitlements', 'LeaveEntitlementBulkAssignFilterForm');

        $this->widgetSchema->setNameFormat('entitlement_filter[%s]');

    }

}


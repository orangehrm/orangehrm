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
 * Description of LeaveEntitlementSearchForm
 *
 */
class LeaveEntitlementSearchForm extends LeaveEntitlementForm {
    
    public function configure() {
        parent::configure();
        $leaveTypeWidget = $this->getWidget('leave_type');
        
        $choices = $leaveTypeWidget->getOption('choices');
        if (!isset($choices[''])) {
            $choices = array('' => 'All') + $choices; 
            $leaveTypeWidget->setOption('choices', $choices);
            $this->setDefault('leave_type', '');
            
            $this->setValidator('leave_type', new sfValidatorChoice(array('choices' => array_keys($choices), 'required' => false)));
        }        
    }    
}


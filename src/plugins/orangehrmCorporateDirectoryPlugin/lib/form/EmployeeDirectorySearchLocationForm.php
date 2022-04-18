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
 * Description of EmployeeSearchLocationForm
 *
 * @author samantha
 */
class EmployeeDirectorySearchLocationForm  extends BaseForm {
    
    public function configure() {

        $options = array();

        $user = sfContext::getInstance()->getUser();
            $options['show_all_locations'] = true;


        $this->setWidget('location', new ohrmDirectoryWidgetOperationalCountryLocationDropDown($options));
        $this->setValidator('location', new sfValidatorString(array('required' => false)));
        $this->getWidgetSchema()->setNameFormat('searchDirectory[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());
        $this->getWidgetSchema()->setFormFormatterName('BreakTags');
    }
    
    /**
     *
     * @return array
     */
    protected function getFormLabels() {

        $labels = array(
            'location' => __('Location')
            
        );
        return $labels;
    }
}

?>

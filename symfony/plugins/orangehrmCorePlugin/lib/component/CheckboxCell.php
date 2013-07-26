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
 * A checkbox cell for the list component. 
 *
 */
class CheckboxCell extends Cell {

    protected function getLabel() {
        if ($this->hasProperty('labelGetter')) {
            $label = $this->getValue('labelGetter');
        } else {
            $label = $this->getPropertyValue('label', 'Undefined');
        }

        return $label;
    }

    public function __toString() {

        $id = $this->getParsedPropertyValue('id');        
        $name = $this->getParsedPropertyValue('name');;
        $value = $this->getParsedPropertyValue('value');;
        $labelName = $this->getParsedPropertyValue('label');
        $checked = $this->getParsedPropertyValue('checked');

        $checkboxAttributes = array(
            'type' => 'checkbox',
            'id' => $id,
            'name' => $name,
            'value' => $value,
        );
        
        if ($checked) {
            $checkboxAttributes['checked'] = 'checked';
        }
    
        $html = tag('input', $checkboxAttributes);
        
        
        if (!empty($labelName)) {
        
            $checkboxBeforeLabel = $this->getParsedPropertyValue('checkboxBeforeLabel', true);

            $labelAttributes = array('for' => $id);
            $label = content_tag('label', $labelName, $labelAttributes);        
            if ($checkboxBeforeLabel) {
                $html = $html . $label;                
            } else {
                $html = $label . $html;
            }
        }
        
        return $html . $this->getHiddenFieldHTML();
    }    

}


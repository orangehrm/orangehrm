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
class ohrmReportWidgetIncludedEmployeesDropDown extends sfWidgetForm implements ohrmEnhancedEmbeddableWidget {

    private $whereClauseCondition;
    
    private $conditionMap = array(1 => 'IS NULL', 2 => NULL, 3 => 'IS NOT NULL');

    public function configure($options = array(), $attributes = array()) {

        $this->addOption('choices', $this->_getIncludedEmployeeOptions());
    }

    public function render($name, $value = null, $attributes = array(), $errors = array()) {
        
        $value = $value === null ? 'null' : $value;

        $options = array();

        foreach ($this->getOption('choices') as $key => $option) {
            $attributes = array('value' => self::escapeOnce($key));
            
            if ($key == $value) {
                $attributes['selected'] = 'selected';
            }
            
            $options[] = $this->renderContentTag(
                            'option',
                            self::escapeOnce($option),
                            $attributes
            );
        }

        $html = $this->renderContentTag(
                        'select',
                        "\n" . implode("\n", $options) . "\n",
                        array_merge(array('name' => $name . '[comparision]'), $attributes
                ));

        return $html;
    }
    
    private function _getIncludedEmployeeOptions() {
        
        $options['1'] = __("Current Employees Only");
        $options['2'] = __("Current and Past Employees");
        $options['3'] = __("Past Employees Only");
        
        return $options;
        
    }

    /**
     * Embeds this widget into the form. Sets label and validator for this widget.
     * @param sfForm $form
     */
    public function embedWidgetIntoForm(sfForm &$form) {



        $widgetSchema = $form->getWidgetSchema();
        $widgetSchema[$this->attributes['id']] = $this;
        $label = __(ucwords(str_replace("_", " ", $this->attributes['id'])));
        
        $required = false;
        
        if (isset($this->attributes['required']) && ($this->attributes['required'] == "true")) {
            $label .= "<span class='required'> * </span>";
            $required = true;
        }
        
        $requiredMess = $label . ' is required.';        
        //$validator = new sfValidatorString(array('required' => $required), array('required' => $requiredMess));  
        $validator = new sfValidatorPass();      
        
        $widgetSchema[$this->attributes['id']]->setLabel($label);
        $form->setValidator($this->attributes['id'], $validator);
    }

    /**
     * Sets whereClauseCondition.
     * @param string $condition
     */
    public function setWhereClauseCondition($condition) {
        
        if (isset($this->conditionMap[$condition])) {
            $this->whereClauseCondition = $this->conditionMap[$condition];
        }

    }

    /**
     * Gets whereClauseCondition. ( if whereClauseCondition is set returns that, else returns default condition )
     * @return string ( a condition )
     */
    public function getWhereClauseCondition() {

        if (isset($this->whereClauseCondition)) {
            $setCondition = $this->whereClauseCondition;
            return $setCondition;
        } else {
            $defaultCondition = NULL;
            return $defaultCondition;
        }
    }

    /**
     * This method generates the where clause part.
     * @param string $fieldName
     * @param string $value
     * @return string
     */
    public function generateWhereClausePart($fieldName, $value) {
        
        if ($this->whereClauseCondition != NULL) {
            $whereClausePart = $fieldName . " " . $this->whereClauseCondition;
        }
        
        return $whereClausePart;
        
    }
    
    public function getDefaultValue(SelectedFilterField $selectedFilterField) {
        
        $condition = array_search($selectedFilterField->whereCondition, $this->conditionMap);            
        if ($condition === false) {
            $condition = NULL;
        }
      
        return $condition;
    }    

}


<?php

class DefineReportForm extends sfForm {

    public $displayFieldGroups = array();
    public $filterWidgets = array();
    public $requiredFilterWidgets = array();
    public $selectedFilterWidgets = array();
    public $selectedDisplayFieldGroups = array();
    public $selectedDisplayFields = array();
    
    public function configure() {

        $this->setWidgets(array());

        $this->widgetSchema->setNameFormat('report[%s]');
        
    }
    
    /**
     * Make selected filter fields required. (If a filter field is available in
     * submitted form values, it is also required).
     * 
     * @param array $taintedValues
     * @param array $taintedFiles 
     */
    public function bind(array $taintedValues = null, array $taintedFiles = null) {

        foreach ($this->filterWidgets as $name => $filter) {
            
            // If it is in the filter widgets array, make it required
            $required = false;
            
            if (isset($taintedValues[$name])) {                                              
                $required = true;                
                $this->selectedFilterWidgets[$name] = $filter;
            }
            
            if (isset($this->validatorSchema[$name])) {
                $this->validatorSchema[$name]->setOption('required', $required);
            }
                
            
        }
        
        $this->updateAvailableFilterWidgets();
        
        parent::bind($taintedValues, $taintedFiles);
    }
    
    public function getSelectedFilterValues() {

        $allValues = $this->getValues();
        
        return array_intersect_key($allValues, $this->selectedFilterWidgets);        

    }
    
    protected function updateAvailableFilterWidgets() {
        // Update criteria_list widget
        $availableFilterWidgets = array_diff($this->filterWidgets, $this->selectedFilterWidgets);
        
        
        $this->setWidget('criteria_list', new sfWidgetFormChoice(array('choices' => $availableFilterWidgets)));        
    }

    public function updateSelectedFilterFields($selectedFilterFieldNames) {
        
        foreach($selectedFilterFieldNames as $selectedFilterField) {
            
            $name = $selectedFilterField->getFilterField()->getName();
            if (isset($this->filterWidgets[$name])) {
                $this->selectedFilterWidgets[$name] = $this->filterWidgets[$name];
                
                $widget = $this->widgetSchema[$name];
                if ($widget instanceof ohrmEnhancedEmbeddableWidget) {

                    $this->setDefault($name, $widget->getDefaultValue($selectedFilterField));
                }
            }
        }
        
        $this->updateAvailableFilterWidgets();

    }
    
    public function updateSelectedDisplayFieldGroups($selectedDisplayFieldGroups) {
        foreach ($selectedDisplayFieldGroups as $group) {
            $this->selectedDisplayFieldGroups[] = $group->getDisplayFieldGroupId();
        }
    }
    
    public function updateSelectedDisplayFields($selectedDisplayFields) {
        foreach ($selectedDisplayFields as $field) {
            $this->selectedDisplayFields[] = $field->getDisplayFieldId();
        }
        $this->setDefault('display_fields', $this->selectedDisplayFields);
    }    
}


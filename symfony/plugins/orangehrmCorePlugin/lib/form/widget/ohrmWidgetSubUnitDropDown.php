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
 * Description of ohrmWidgetSubUnit
 *
 */
class ohrmWidgetSubUnitDropDown extends sfWidgetFormSelect {
    
    private $companyStructureService;
    
    private $choices = null;
    
    public function setCompanyStructureService(CompanyStructureService $service) {
        $this->companyStructureService = $service;
    }
    
    public function getCompanyStructureService() {
        
        if (empty($this->companyStructureService)) {
            $this->companyStructureService = new CompanyStructureService();
        }
        return $this->companyStructureService;
    }
  
    protected function configure($options = array(), $attributes = array()) {
                
        parent::configure($options, $attributes);
        
        //
        // option value for 'all' checkbox. Set to a valid option to enable the 'All' option
        //
        $this->addOption('show_all_option', true);
        $this->addOption('all_option_label', __('All'));

        $this->addOption('show_root', false);
        
        $this->addOption('indent', true);
        $this->addOption('indent_string', "&nbsp;&nbsp;");

        // Parent requires the 'choices' option.
        $this->addOption('choices', array());

    }
    
    /**
     * Get array of subunit choices
     */
    public function getChoices() {
        
        if (is_null($this->choices)) {
            $choices = array();

            $indent = $this->getOption('indent');
            $indentWith = $this->getOption('indent_string');
            $showRoot = $this->getOption('show_root');


            if ($this->getOption('show_all_option')) {
                $choices[0] = $this->getOption('all_option_label');
            }

            $treeObject = $this->getCompanyStructureService()->getSubunitTreeObject();
            $tree = $treeObject->fetchTree();

            foreach ($tree as $node) {
                if ($node->getId() != 1) {

                    $label = $node['name'];
                    if ($indent) {
                        $label = str_repeat($indentWith, $node['level'] - 1) . $label;
                    }

                    $choices[$node->getId()] = $label;
                }
            }
            
            $this->choices = $choices;            
        }
        
//        asort($this->choices);
        return $this->choices;               
    }
    
    public function getValidValues() {
        $choices = $this->getChoices();
        return array_keys($choices);
    }
    
    
}


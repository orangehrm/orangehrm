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
 * Description of KpiListConfigurationFactory
 *
 */
class KpiListConfigurationFactory extends ohrmListConfigurationFactory {

    protected function init() {

        $headerArray = array();
        
        $header = new ListHeader();

        $header->populateFromArray(array(
            'name' => 'Key Performance Indicator',
            'width' => '40%',
            'isSortable' => false,
            'sortField' => null,
            'elementType' => 'link',
            'elementProperty' => array(
                'labelGetter' => 'getKpiIndicators',
                'placeholderGetters' => array('id' => 'getId'),
                'urlPattern' => 'index.php/performance/saveKpi?hdnEditId={id}'),
        ));
        
        $headerArray [] = $header;
        
        $header = new ListHeader();
        $header->populateFromArray(array(
            'name' => 'Job Title',
            'width' => '20%',
            'isSortable' => false,
            'sortField' => null,
            'elementType' => 'label',
            'elementProperty' => array(
                'getter' => array('getJobTitle', 'getJobTitleName'))
                
        ));        
        $headerArray [] = $header;   
        
        $header = new ListHeader();
        $header->populateFromArray(array(
            'name' => 'Department',
            'width' => '10%',
            'isSortable' => false,
            'sortField' => null,
            'elementType' => 'label',
            'elementProperty' => array(
                'getter' => array('getDepartment', 'getName'))
                
        ));
        $headerArray [] = $header; 
        
        $header = new ListHeader();
        $header->populateFromArray(array(
            'name' => 'Min Rate',
            'width' => '5%',
            'isSortable' => false,
            'sortField' => null,
            'elementType' => 'label',
            'textAlignmentStyle' => 'center',
            'elementProperty' => array('getter' => 'getMinRating')
                
        ));

        $headerArray [] = $header;   
        $header = new ListHeader();
        $header->populateFromArray(array(
            'name' => 'Max Rate',
            'width' => '5%',
            'isSortable' => false,
            'textAlignmentStyle' => 'center',
            'sortField' => null,
            'elementType' => 'label',
            'elementProperty' => array('getter' => 'getMaxRating')
                
        ));
        $headerArray [] = $header;     
        
        $header = new ListHeader();
        $header->populateFromArray(array(
            'name' => 'Is Default',
            'width' => '10%',
            'isSortable' => false,
            'sortField' => null,           
            'filters' => array('EnumCellFilter' => array(
                                                    'enum' => array(0 => 'No', 1 => 'Yes'), 
                                                    'default' => ''),
                               'I18nCellFilter' => array()
                              ),            
            'elementType' => 'label',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array('getter' => 'getDefaultKpi'),
        ));
         $headerArray [] = $header;     
        
        $this->headers = $headerArray;
    }

    /**
     *
     * @return string 
     */
    public function getClassName() {
        return 'Kpi';
    }
}


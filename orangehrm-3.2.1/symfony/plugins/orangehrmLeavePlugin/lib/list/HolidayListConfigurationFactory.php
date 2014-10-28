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
 * Description of HolidayListConfigurationFactory
 *
 */
class HolidayListConfigurationFactory extends ohrmListConfigurationFactory {
    private $isLinkable;

    protected function init() {

        $header1 = new ListHeader();

        $header1->populateFromArray(array(
            'name' => 'Name',
            'width' => '40%',
            'isSortable' => false,
            'sortField' => null,
            'elementType' => 'link',
            'elementProperty' => array(
                'linkable' => $this->isLinkable,
                'labelGetter' => 'getDescription',
                'placeholderGetters' => array('id' => 'getId'),
                'urlPattern' => 'index.php/leave/defineHoliday?hdnEditId={id}'),
        ));

        $header2 = new ListHeader();

        $header2->populateFromArray(array(
            'name' => 'Date',
            'width' => '25%',
            'isSortable' => false,
            'sortField' => null,
            'filters' => array('DateCellFilter' => array()),            
            'elementType' => 'label',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array('getter' => 'getDate'),
        ));
        
        $header3 = new ListHeader();

        $header3->populateFromArray(array(
            'name' => 'Full Day/Half Day',
            'width' => '20%',
            'isSortable' => false,
            'sortField' => null,
            'filters' => array('EnumCellFilter' => array(
                                                    'enum' => PluginWorkWeek::getDaysLengthList(), 
                                                    'default' => ''),
                               'I18nCellFilter' => array()
                              ),
            'elementType' => 'label',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array('getter' => 'getLength'),
        ));

        $header4 = new ListHeader();

        $header4->populateFromArray(array(
            'name' => 'Repeats Annually',
            'width' => '15%',
            'isSortable' => false,
            'sortField' => null,
            'filters' => array('EnumCellFilter' => array(
                                                    'enum' => PluginWorkWeek::getYesNoList(), 
                                                    'default' => ''),
                               'I18nCellFilter' => array()
                              ),            
            'elementType' => 'label',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array('getter' => 'getRecurring'),
        ));
        
        $this->headers = array($header1, $header2, $header3, $header4);
    }

    public function getClassName() {
        return 'HolidayList';
    }
    
    public function setIsLinkable($isLinkable) {
        $this->isLinkable = $isLinkable;
    }
    
    public function getIsLinkable() {
        return $this->isLinkable;
    }    

}


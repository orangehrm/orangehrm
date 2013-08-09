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
 *
 */

/**
 * Description of LeaveEntitlementListConfigurationFactory
 */
class LeaveEntitlementListConfigurationFactory extends ohrmListConfigurationFactory {
    
    protected $allowEdit;
    
    public static $displayLeaveType = false;
    
    public function init() {
        sfContext::getInstance()->getConfiguration()->loadHelpers('OrangeDate');
        
        $headers = array();
        
        $header1 = new ListHeader();
        $header2 = new ListHeader();
        $header3 = new ListHeader();
        $header4 = new ListHeader();
        
        $widthPercentages = self::$displayLeaveType ? array('20%', '35%', '20%', '20%', '5%') :           
                array('45%', '25%', '25%', '5%');
        
        if (self::$displayLeaveType) {
            
            $leaveTypeHeader = new ListHeader();
            $leaveTypeHeader->populateFromArray(array(
                'name' => 'Leave Type',
                'width' => array_shift($widthPercentages),
                'isSortable' => false,
                'elementType' => 'label',
                'textAlignmentStyle' => 'left',
                'elementProperty' => array('getter' => array('getLeaveType', 'getDescriptiveLeaveTypeName'))
            ));
            
            $headers[] = $leaveTypeHeader;            
        } else {
            $widthPercentages = array('45%', '25%', '25%', '5%');
        }       
            
        $header1->populateFromArray(array(
            'name' => 'Entitlement Type',
            'width' => array_shift($widthPercentages),
            'isSortable' => false,
            'elementType' => 'label',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array('getter' => array('getLeaveEntitlementType', 'getName')) 
        ));
        $headers[] = $header1;        

        $header2->populateFromArray(array(
            'name' => 'Valid From',
            'width' => array_shift($widthPercentages),
            'isSortable' => false,
            'elementType' => 'linkDate',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'linkable' => $this->allowEdit,
                'labelGetter' => 'getFromDate',
                'placeholderGetters' => array('id' => 'getId'),
                'urlPattern' => public_path('index.php/leave/editLeaveEntitlement/id/{id}')                
            )
        ));
        $headers[] = $header2;

        $header3->populateFromArray(array(
            'name' => 'Valid To',
            'width' => array_shift($widthPercentages),
            'isSortable' => false,
            'elementType' => 'linkDate',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'linkable' => $this->allowEdit,
                'labelGetter' => 'getToDate',
                'placeholderGetters' => array('id' => 'getId'),
                'urlPattern' => public_path('index.php/leave/editLeaveEntitlement/id/{id}'),                
            )
        ));
        $headers[] = $header3;
        
        $header4->populateFromArray(array(
            'name' => 'Days',
            'width' => array_shift($widthPercentages),
            'isSortable' => false,
            'elementType' => 'link',
            'textAlignmentStyle' => 'right',
            'filters' => array('NumberFormatCellFilter' => array()),              
            'elementProperty' => array(
                'linkable' => $this->allowEdit,
                'labelGetter' => array('getNoOfDays'),
                'placeholderGetters' => array('id' => 'getId'),
                'urlPattern' => public_path('index.php/leave/editLeaveEntitlement/id/{id}'),
            ),            
        ));
        $headers[] = $header4;

        $this->headers = $headers;       
    }
    
    public function getClassName() {
        return 'LeaveEntitlement';
    }
    
    public function getAllowEdit() {
        return $this->allowEdit;
    }

    public function setAllowEdit($allowEdit) {
        $this->allowEdit = $allowEdit;
    }

    
}

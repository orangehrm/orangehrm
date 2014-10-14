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
 * Description of SearchReviewListConfigurationFactory
 *
 */
class SearchReviewListConfigurationFactory extends ohrmListConfigurationFactory {

    protected function init() {

        $headerArray = array();
        $header1 = new ListHeader();
        $header2 = new DueDateHeader();
        $header3 = new ReviewPeriodHeader();
        $header4 = new ListHeader();
        $header6 = new ManagePerformanceActionHeader();
        $header5 = new ListHeader();

        $header1->populateFromArray(array(
            'name' => 'Employee',
            'isSortable' => true,
            'sortField' => 'employeeId',
            'elementType' => 'label',
            'elementProperty' => array('getter' => array('getEmployee', 'getFullName')),
        ));


        $header2->populateFromArray(array(
            'name' => 'Due Date',
            'isSortable' => true,
            'sortField' => 'due_date',
            'elementType' => 'DueDate',
        ));

        $header3->populateFromArray(array(
            'name' => 'Review Period',
            'isSortable' => false,
            'sortField' => null,
            'elementType' => 'ReviewPeriod',
            'elementProperty' => array('getter' => 'getWorkPeriodStart'),
        ));

        $header4->populateFromArray(array(
            'name' => 'Work Period End Date',
            'isSortable' => false,
            'sortField' => null,
            'elementType' => 'label',
            'elementProperty' => array('getter' => 'getWorkPeriodEnd'),
        ));


        $header4->populateFromArray(array(
            'name' => 'Job Title',
            'isSortable' => false,
            'sortField' => null,
            'elementType' => 'label',
            'elementProperty' => array('getter' => array('getJobTitle', 'getJobTitle')),
        ));

        $header6->populateFromArray(array(
            'name' => 'Action',
            'isSortable' => false,
            'sortField' => null,
            'elementType' => 'ManagePerformanceAction',
            'elementProperty' => array(
                'placeholderGetters' => array('id' => 'getId'),
                'urlPattern' => 'index.php/performance/performanceReviewProgress?id={id}'),
        ));

        $reviewStatus = array();
        $reviewStatus [ReviewStatusActivated::getInstance()->getStatusId()] = ReviewStatusActivated::getInstance()->getName();
        $reviewStatus [ReviewStatusApproved::getInstance()->getStatusId()] = ReviewStatusApproved::getInstance()->getName();
        $reviewStatus [ReviewStatusInProgress::getInstance()->getStatusId()] = ReviewStatusInProgress::getInstance()->getName();
        $reviewStatus [ReviewStatusInactive::getInstance()->getStatusId()] = ReviewStatusInactive::getInstance()->getName();

        $header5->populateFromArray(array(
            'name' => 'Status',
            'width' => '10%',
            'isSortable' => false,
            'sortField' => null,
            'filters' => array('EnumCellFilter' => array(
                    'enum' => $reviewStatus,
                    'default' => ''),
                'I18nCellFilter' => array()
            ),
            'elementType' => 'label',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array('getter' => 'getStatusId'),
        ));

        $this->headers = array($header1, $header2, $header3, $header4, $header5, $header6);
    }

    /**
     *
     * @return string 
     */
    public function getClassName() {
        return 'ReviewList';
    }

}

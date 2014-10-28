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
 * Description of employeeDistributionAction
 */
class employeeDistributionAction extends BaseDashboardAction {

    public function preExecute() {
        $this->setLayout(false);
        parent::preExecute();
    }

    public function execute($request) {
        $this->data = $this->getGraphService()->getEmployeeCountBySubUnit();
        if (count($this->data) > 0) {
            $pieChart = new PieChart();
            $pieChart->setChartNumber('emp_distribution');
            $pieChart->setWidth(300);
            $pieChart->setHeight(225);
            $pieChart->setStyles(array('margin-top' => '10px'));
            $dataFormatter = new GraphDataFormatter();
            $dataFormatter->setGroupMappings(array(
                'default-label' => '---',
                'label-index' => 'name',
                'value-index' => 'COUNT',
            ));
            $pieChart->setDataFormatter($dataFormatter);
            $metaDataObject = new GraphMetaData();
            $legend = new GraphLegendData();
            $legend->setLegendDivId('div_legend_pim_employee_distribution');
            $legend->setUseSeparateContainer(true);
            $legend->setLabels($dataFormatter->extractLabels($this->data, 'name'));
            $metaDataObject->setLegend($legend);
            $pieChart->setData($this->data);
            $pieChart->setPropertes(array(
                'show-legend' => true,
                'show-labels' => true,
                'interactive' => true,
                'suffixForValueHover' => 'Employee(s)',
            ));
            $pieChart->setMetaDataObject($metaDataObject);

            $this->chart = $pieChart;
        }
    }

}

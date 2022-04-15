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
 * Boston, MA 02110-1301, USA
 */

use OrangeHRM\Maintenance\Service\MaintenanceService;

/**
 * Class PurgeCandidateForm
 */
class PurgeCandidateForm extends BaseForm
{
    private $maintenanceService = null;

    /**
     * @configure function of form
     */
    public function configure()
    {
        $this->setWidgets($this->getWidgetList());
        $this->setValidators($this->getValidatorList());
        $this->getWidgetSchema()->setLabels($this->getLabelList());
    }

    /**
     * @return array
     * @throws DaoException
     */
    public function getWidgetList()
    {
        $widgets = array();
        $widgets['candidate'] = new ohrmWidgetEmployeeNameAutoFill(array('jsonList' => $this->getVacancyListAsJson()));
        return $widgets;
    }

    /**
     * @return array
     */
    public function getValidatorList()
    {
        $validators = array();
        $validators['candidate'] = new ohrmValidatorEmployeeNameAutoFill(array('required' => true));
        return $validators;
    }

    /**
     * @return array
     */
    public function getLabelList()
    {
        $requiredMarker = ' <em>*</em>';
        $lableList = array();
        $lableList['candidate'] = __('Select Vacancy') . $requiredMarker;
        return $lableList;
    }

    /**
     * @return string
     * @throws DaoException
     */
    protected function getVacancyListAsJson()
    {
        $vacancyList = $this->getMaintenanceService()->getVacancyListToPurge();
        $jsonArray = array();
        foreach ($vacancyList as $vacancy) {
            $vacancyId = $vacancy['id'];
            $vacancyname = trim(trim($vacancy['name']));
            $jsonArray[] = array('name' => $vacancyname, 'id' => $vacancyId);
        }
        $jsonString = json_encode($jsonArray);
        return $jsonString;
    }

    /**
     * @return MaintenanceService|null
     */
    public function getMaintenanceService()
    {
        if (!isset($this->maintenanceService)) {
            $this->maintenanceService = new MaintenanceService();
        }
        return $this->maintenanceService;
    }
}

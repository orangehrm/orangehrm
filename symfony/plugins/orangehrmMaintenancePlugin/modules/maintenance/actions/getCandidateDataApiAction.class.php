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

/**
 * Class getCandidateDataApiAction
 */
class getCandidateDataApiAction extends sfAction
{
    private $maintenanceService = null;

    /**
     * @param sfRequest $request
     * @return mixed|void
     */
    public function execute($request)
    {
        $this->getUser()->setFlash('warning', null);
        $this->getUser()->setFlash('success', null);

        $data = $request->getParameterHolder()->getAll();

        $candidate = $this->getmaintenanceService()->getDeniedCandidatesToKeepDataByVacnacyId($data['vacancyID']);
        $this->_setListComponent($candidate, 1, 1);
    }

    /**
     * @return MaintenanceService|null
     */
    public function getmaintenanceService()
    {
        if (!isset($this->maintenanceService)) {
            $this->maintenanceService = new MaintenanceService();
        }
        return $this->maintenanceService;
    }

    /**
     * @param $candidates
     * @param $noOfRecords
     * @param $pageNumber
     */
    public function _setListComponent($candidates, $noOfRecords, $pageNumber)
    {
        $buttons = array();
        if (count($candidates)) {
            $buttons['Delete'] = array('label' => 'Purge',
                'id' => 'btnPurge',
                'data-toggle' => 'modal',
                'data-target' => '#deleteConfModal',
                'class' => 'delete');
        }
        $configurationFactory = new ApplicantForVacancyList();
        $configurationFactory->setRuntimeDefinitions(array(
            'hasSelectableRows' => false,
            'buttons' => $buttons
        ));
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($candidates);
    }
}

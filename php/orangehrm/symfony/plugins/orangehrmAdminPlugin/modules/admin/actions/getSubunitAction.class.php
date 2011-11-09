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

class getSubunitAction extends sfAction{

     private $companyStructureService;

    public function getCompanyStructureService() {
        if (is_null($this->companyStructureService)) {
            $this->companyStructureService = new CompanyStructureService();
            $this->companyStructureService->setCompanyStructureDao(new CompanyStructureDao());
        }
        return $this->companyStructureService;
    }

    public function setCompanyStructureService(CompanyStructureService $companyStructureService) {
        $this->companyStructureService = $companyStructureService;
    }

    public function execute($request) {
        $subunitId = (int) $request->getParameter('subunitId');

        $object = new stdClass();

        try {
            $subunit = $this->getCompanyStructureService()->getSubunitById($subunitId);
            $object->id = $subunit->getId();
            $object->name = $subunit->getName();
            $object->description = $subunit->getDescription();
            $object->unitId = $subunit->getUnitId();

        } catch (Exception $e) {
            $object->message = __('Failed to load subunit');
            $object->messageType = 'failure';
        }

        @ob_clean();
        return $this->renderText(json_encode($object));
    }

}


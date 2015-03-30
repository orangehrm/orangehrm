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
class viewCompanyStructureAction extends sfAction {

    private $companyStructureService;
    private $organizationService;

    public function getOrganizationService() {
        if (is_null($this->organizationService)) {
            $this->organizationService = new OrganizationService(new OrganizationDao());
        }
        return $this->organizationService;
    }


    /**
     * 
     * @return CompanyStructureService 
     */
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

        $usrObj = $this->getUser()->getAttribute('user');
        if (!($usrObj->isAdmin())) {
            $this->redirect('pim/viewPersonalDetails');
        }
        
        $this->isOrganizationNameSet = false;
        $organization = $this->getOrganizationService()->getOrganizationGeneralInformation();
        if($organization instanceof Organization && $organization->getName() != null){
            $this->isOrganizationNameSet = true;
        }
        
        $treeObject = $this->getCompanyStructureService()->getSubunitTreeObject();
        $tree = new ohrmTreeViewComponent();
        $tree->getPropertyObject()->setTreeObject($treeObject);
        $this->tree = $tree;

        $this->form = new SubunitForm(array(),array(),true);
        
        $this->listForm = new DefaultListForm();
    }

}


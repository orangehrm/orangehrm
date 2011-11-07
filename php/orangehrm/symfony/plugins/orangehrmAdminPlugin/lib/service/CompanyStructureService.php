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

class CompanyStructureService extends BaseService {

    private $companyStructureDao;

    public function getCompanyStructureDao() {
        if (!($this->companyStructureDao instanceof CompanyStructureDao)) {
            $this->companyStructureDao = new CompanyStructureDao();
        }
        return $this->companyStructureDao;
    }

    public function setCompanyStructureDao(CompanyStructureDao $dao) {
        $this->companyStructureDao = $dao;
    }

    /**
     * Get sub unit for a given id
     *
     * @version
     * @param int $id Subunit auto incremental id
     * @return Subunit instance if found or a dao exception
     */
    public function getSubunitById($id) {
        return $this->getCompanyStructureDao()->getSubunitById($id);
    }

    /**
     * Save a Subunit
     *
     * If id is not set, it will be set to next available value and a new subunit
     * will be added.
     *
     * If id is set the belonged subunit will be updated.
     *
     * @version
     * @param Subunit $subunit
     * @return boolean
     */
    public function saveSubunit(Subunit $subunit) {
        return $this->getCompanyStructureDao()->saveSubunit($subunit);
    }


    public function addSubunit(Subunit $parentSubunit, Subunit $subunit) {
        return $this->getCompanyStructureDao()->addSubunit($parentSubunit, $subunit);
    }

    public function deleteSubunit(Subunit $subunit) {
        return $this->getCompanyStructureDao()->deleteSubunit($subunit);
    }

    public function setOrganizationName($name){
        return $this->getCompanyStructureDao()->setOrganizationName($name);
    }

    public function getSubunitTreeObject(){
       return $this->getCompanyStructureDao()->getSubunitTreeObject();
    }

}

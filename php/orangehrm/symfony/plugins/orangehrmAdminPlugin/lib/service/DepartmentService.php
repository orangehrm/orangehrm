<?php

class DepartmentService extends BaseService {

    private $departmentDao;

    public function getDepartmentDao() {
        if (!(isset($this->departmentDao) && $this->departmentDao instanceof DepartmentDao)) {
            $this->departmentDao = new DepartmentDao();
        }
        return $this->departmentDao;
    }

    public function setDepartmentDao(DepartmentDao $dao) {
        $this->departmentDao = $dao;
    }

    /**
     *
     * @param String $id
     * @return Department
     */
    public function readDepartment($id) {
        return $this->getDepartmentDao()->readDepartment($id);
    }

    public function saveDepartment(Department $department) {
        return $this->getDepartmentDao()->saveDepartment($department);
    }


    public function addDepartment(Department $parentDepartment, Department $department) {
        return $this->getDepartmentDao()->addDepartment($parentDepartment, $department);
    }


    public function deleteDepartment(Department $department) {
        return $this->getDepartmentDao()->deleteDepartment($department);
    }

    public function setOrganizationName($name){
        return $this->getDepartmentDao()->setOrganizationName($name);
    }

}

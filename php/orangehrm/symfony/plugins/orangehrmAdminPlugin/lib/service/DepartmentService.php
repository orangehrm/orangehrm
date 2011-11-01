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
        try {
            return $this->getDepartmentDao()->readDepartment($id);
        } catch (Exception $e) {
            throw new AdminServiceException($e->getMessage());
        }
    }

    /**
     *
     * @param Department $nstitution
     * @return boolean
     * @throws NUSAdminServiceException
     */
    public function saveDepartment(Department $department) {
        try {
            return $this->getDepartmentDao()->saveDepartment($department);
        } catch (Exception $e) {
            throw new NUSAdminServiceException($e->getMessage());
        }
    }

    /**
     *
     * @param Department $parentDepartment
     * @param Department $department
     * @return boolean
     * @throws  NUSAdminServiceException
     */
    public function addDepartment(Department $parentDepartment, Department $department) {
        try {
            return $this->getDepartmentDao()->addDepartment($parentDepartment, $department);
        } catch (Exception $e) {
            throw new NUSAdminServiceException($e->getMessage());
        }
    }

    /**
     *
     * @param Department $department
     * @return boolean
     * @throws NUSAdminServiceException
     */
    public function deleteDepartment(Department $department) {
        try {
            return $this->getDepartmentDao()->deleteDepartment($department);
        } catch (Exception $e) {
            throw new NUSAdminServiceException($e->getMessage());
        }
    }

}

<?php

class DepartmentDao extends BaseDao {

    /**
     * Reads Department by Id.
     * @param String $id
     * @return Department
     * @throws DaoException
     */
    public function readDepartment($id) {
        try {
            return Doctrine::getTable('Department')->find($id);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param Department $department
     * @return boolean
     * @throws DaoException
     */
    public function saveDepartment(Department $department) {
        try {

            if ($department->getId() == '') {
                $department->setId(0);
            } else {
                $tempObj = Doctrine::getTable('Department')->find($department->getId());

                $tempObj->setName($department->getName());
                $tempObj->setDescription($department->getDescription());
                $tempObj->setUnitId($department->getUnitId());
                $department = $tempObj;
            }

            $department->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function addDepartment(Department $parentDepartment, Department $department) {
        try {

            $department->setId(0);

            $treeObject = Doctrine::getTable('Department')->getTree();

            $department->getNode()->insertAsLastChildOf($parentDepartment);

            $parentDepartment->setRgt($parentDepartment->getRgt() + 2);
            $parentDepartment->save();

            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param Department $department
     * @return boolean
     * @throws DaoException
     */
    public function deleteDepartment(Department $department) {
        try {

            $q = Doctrine_Query::create()
                            ->delete('Department')
                            ->where('lft >= ?', $department->getLft())
                            ->andWhere('rgt <= ?', $department->getRgt());
            $q->execute();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

}

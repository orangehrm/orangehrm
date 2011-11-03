<?php

class CompanyStructureDao extends BaseDao {

    public function getSubunit($id) {
        try {
            return Doctrine::getTable('Subunit')->find($id);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function saveSubunit(Subunit $subunit) {
        try {

            if ($subunit->getId() == '') {
                $subunit->setId(0);
            } else {
                $tempObj = Doctrine::getTable('Subunit')->find($subunit->getId());

                $tempObj->setName($subunit->getName());
                $tempObj->setDescription($subunit->getDescription());
                $tempObj->setUnitId($subunit->getUnitId());
                $subunit = $tempObj;
            }

            $subunit->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function addSubunit(Subunit $parentSubunit, Subunit $subunit) {
        try {

            $subunit->setId(0);

            $treeObject = Doctrine::getTable('Subunit')->getTree();

            $subunit->getNode()->insertAsLastChildOf($parentSubunit);

            $parentSubunit->setRgt($parentSubunit->getRgt() + 2);
            $parentSubunit->save();

            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function deleteSubunit(Subunit $subunit) {
        try {

            $q = Doctrine_Query::create()
                            ->delete('Subunit')
                            ->where('lft >= ?', $subunit->getLft())
                            ->andWhere('rgt <= ?', $subunit->getRgt());
            $q->execute();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function setOrganizationName($name){
        try {
        $q = Doctrine_Query:: create()->update('Subunit')
                    ->set('name', '?', $name)
                    ->where('id = 1');
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

}

<?php

/**
 * CustomFieldsDao to make CRUD operations
 *
 * @author Sujith T
 */
class CustomFieldsDao extends BaseDao {

    /**
     * Retrieve Custom Fields
     * @param String $orderField
     * @param String $orderBy
     * @returns Collection
     * @throws DaoException
     */
    public function getCustomFieldList($screen = null, $orderField = "field_num", $orderBy = "ASC") {
        try {
            $q = Doctrine_Query::create()
                            ->from('CustomFields');

            if (!empty($screen)) {
                $q->where('screen = ?', $screen);
            }

            $q->orderBy($orderField . ' ' . $orderBy);

            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Save CustomFields
     * @param CustomFields $customFields
     * @returns boolean
     * @throws DaoException, DataDuplicationException
     */
    public function saveCustomField(CustomFields $customFields) {
        try {
            $q = Doctrine_Query::create()
                            ->from('CustomFields c')
                            ->where('c.name = ?', $customFields->name)
                            ->andWhere('c.field_num <> ?', $customFields->field_num);

            if ($q->count() > 0) {
                throw new DataDuplicationException("Saving CustomFields failed due to saving of dupliced data");
            }

            $freeNum = null;

            if (empty($customFields->field_num)) {
                $q = Doctrine_Query::create()
                                ->select('c.field_num')
                                ->from('CustomFields c')
                                ->orderBy('field_num');
                $fieldNumbers = $q->execute(array(), Doctrine::HYDRATE_SCALAR);
                $count = count($fieldNumbers);

                $i = 1;
                foreach ($fieldNumbers as $num) {

                    if ($num['c_field_num'] > $i) {
                        $freeNum = $i;
                        break;
                    }
                    $i++;

                    if ($i > 10) {
                        break;
                    }
                }

                if (empty($freeNum) && ($i <= 10)) {
                    $freeNum = $i;
                }

                $customFields->field_num = $freeNum;
            }

            if (!empty($customFields->field_num)) {
                $customFields->save();
            }

            return true;
        } catch (Doctrine_Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Delete CustomField
     * @param array() $customFieldList
     * @returns boolean
     * @throws DaoException
     */
    public function deleteCustomField($customFieldList = array()) {
        try {
            $this->deleteReletedEmployeeCustomField($customFieldList);

            if (is_array($customFieldList)) {
                $q = Doctrine_Query::create()
                                ->delete('CustomFields')
                                ->whereIn('field_num', $customFieldList);

                $numDeleted = $q->execute();
                if ($numDeleted > 0) {
                    return true;
                }
            }
            return false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Delete Releted Employee Custom Field
     * @param array() $customFieldList
     * @returns boolean
     * @throws DaoException
     */
    public function deleteReletedEmployeeCustomField($customFieldList = array()) {

        try {
            foreach ($customFieldList as $customField) {
                $actualFieldName = "custom" . $customField;

                $q = Doctrine_Query::create()
                                ->update('Employee')
                                ->set($actualFieldName, '?', '');

                $rows = $q->execute();
            }
            if ($rows > 0) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Returns CustomField by Id. This need to be update to retrieve entity object
     * @param int $id
     * @returns CustomFields array
     * @throws DaoException
     */
    public function readCustomField($id) {
        try {
            return Doctrine::getTable('CustomFields')->find($id);
        } catch (Exception $e) {
            throw new AdminServiceException($e->getMessage());
        }
    }

}

?>

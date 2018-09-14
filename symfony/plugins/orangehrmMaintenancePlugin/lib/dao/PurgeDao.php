<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 3/9/18
 * Time: 11:19 AM
 */
class PurgeDao extends BaseDao
{
    /**
     * @param $empNumber
     * @return array|Doctrine_Record
     * @throws DaoException
     */
    public function getSoftDeletedEmployee($empNumber)
    {
        try {
            $q = Doctrine_Query:: create()
                ->from('Employee e')
                ->leftJoin('e.jobTitle jt')
                ->where('e.empNumber = ?', $empNumber);
            return $q->fetchOne();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function isEmployeeIdExists($employeeId)
    {
        try {
            $q = Doctrine_Query::create()
                ->from('Employee e')
                ->where('e.employee_id = ?', $employeeId);
            $employeeCount = $q->count();
            return $employeeCount > 0;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function saveEntity($entity)
    {
        try {
            $entity->save();
            return $entity;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function replaceEntityValues($entityClassName, $fieldValueArray, $matchByValuesArray)
    {
//        var_dump($entityClassName, $fieldValueArray, $matchByValuesArray);die;
        try {
            $q = Doctrine_Query::create()
                ->update($entityClassName);

            foreach ($fieldValueArray as $field => $value) {
                if (is_null($value)) {
                    $q->set($field, new Doctrine_Expression('NULL'));
                } else {
                    $q->set($field, "?", $value);
                }
            }
            foreach ($matchByValuesArray as $field => $value) {
                if (is_array($value)) {
                    $q->andWhereIn($field, $value);
                } else {
                    $q->andWhere($field . " = ?", $value);
                }
            }
//            var_dump($q->execute())
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd

    }

    public function removeEntities($entityClassName, $matchValuesArray) {
        try{
            $q = Doctrine_Query::create()
                ->delete($entityClassName);
            foreach ($matchValuesArray as  $field => $value) {
                if(is_array($value)) {
                    $q->whereIn($field,$value);
                } else {
                    $q->where($field." = ?", $value);
                }
            }
            var_dump($q->execute(), $entityClassName, $matchValuesArray);
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

}

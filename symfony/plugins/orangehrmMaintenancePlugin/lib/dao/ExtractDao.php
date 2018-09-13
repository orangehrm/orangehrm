<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 7/9/18
 * Time: 3:12 PM
 */
class ExtractDao extends BaseDao{
    public function extractDataFromEmployeeNum($empNumber,$table)
    {
        try {
            return Doctrine :: getTable($table)->find($empNumber);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
}
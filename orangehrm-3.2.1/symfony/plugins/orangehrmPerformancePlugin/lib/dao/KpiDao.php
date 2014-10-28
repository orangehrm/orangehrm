<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2010 OrangeHRM Inc., http://www.orangehrm.com
 *
 * Please refer the file license/LICENSE.TXT for the license which includes terms and conditions on using this software.
 *
 * */

/**
 * Description of KpiDao
 *
 * @author samantha
 */
class KpiDao extends BaseDao {

    /**
     *
     * @param sfDoctrineRecord $kpi
     * @return \sfDoctrineRecord
     * @throws DaoException 
     */
    public function saveKpi(sfDoctrineRecord $kpi) {
        try {

            if ($kpi->getDefaultKpi() > 0) {
                $query = Doctrine_Query :: create()
                        ->update('Kpi k')
                        ->set('default_kpi', 'null');
                $query->execute();
            }

            $kpi->save();
            $kpi->refresh();
            return $kpi;
            //@codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }//@codeCoverageIgnoreEnd
    }

    /**
     *
     * @param array $parameters
     * @return Doctrine_Collection
     * @throws DaoException 
     */
    public function searchKpi($parameters = null) {
        try {
            $query = Doctrine_Query:: create()->from('Kpi');
            
            $offset = ($parameters['page'] > 0) ? (($parameters['page'] - 1) * $parameters['limit']) : 0;

            if (!empty($parameters)) {
                if (isset($parameters['id']) && $parameters['id'] > 0) {
                    $query->andWhere('id = ?', $parameters['id']);
                    return $query->fetchOne();
                } else {
                    foreach ($parameters as $key => $parameter) {
                        if (strlen(trim($parameter)) > 0) {
                            switch ($key) {
                                case 'jobCode':
                                    $query->andWhere('jobTitleCode = ?', $parameter);
                                    break;
                                case 'isDefault':
                                    $query->andWhere('default_kpi = ?', $parameter);
                                    break;
                                default:
                                    break;
                            }
                        }
                    }
                }
            }
            $query->andWhere('deleted_at IS NULL');
            $query->offset($offset);
            
            if ($parameters['limit'] != null) {
                $query->limit($parameters['limit']);
            }
            $query->orderBy('kpi_indicators');
            return $query->execute();
            //@codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }//@codeCoverageIgnoreEnd
    }

    /**
     *
     * @param array $ids
     * @throws DaoException 
     */
    public function deleteKpi($ids) {
        try {
            if (sizeof($ids)) {
                $q = Doctrine_Query::create()
                        ->delete('Kpi')
                        ->whereIn('id', $ids);
                $q->execute();
            }
            return true;
            //@codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }//@codeCoverageIgnoreEnd
    }

    /**
     * Search KPI by Jobtitle
     *
     * @param array $parameters
     * @return Doctrine_Collection
     * @throws DaoException 
     */
    public function searchKpiByJobTitle($parameters = null) {
        try {

            $query = Doctrine_Query:: create()->from('Kpi');
            $query->where('((jobTitleCode = ?) AND (deleted_at IS NULL))', array($parameters['jobCode']));

            $query->orderBy('kpi_indicators');
            return $query->execute();
            //@codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }//@codeCoverageIgnoreEnd
    }
    
    public function getKpiById($id){
        try{
            $result = Doctrine :: getTable('Kpi')->find($id);
            return $result;
            //@codeCoverageIgnoreStart
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage(), $ex->getCode(), $ex);
        }//@codeCoverageIgnoreEnd
    }

}
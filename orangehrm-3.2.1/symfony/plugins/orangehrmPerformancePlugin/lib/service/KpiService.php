<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of KpiService
 *
 * @author nadeera
 */
class KpiService {

    public $dao;

    /**
     *
     * @return KpiDao
     */
    public function getDao() {
        if ($this->dao != null) {
            return $this->dao;
        } else {
            return new KpiDao();
        }
    }

    /**
     *
     * @param KpiDao $dao 
     */
    public function setDao($dao) {
        $this->dao = $dao;
    }

    /**
     *
     * @param Kpi $performanceReviewTemplate
     * @return Kpi 
     */
    public function saveKpi($kpi) {
        return $this->getDao()->saveKpi($kpi);
    }

    /**
     *
     * @param array $parameters
     * @return Doctrine_Collection 
     */
    public function searchKpi($parameters = null) {
        return $this->getDao()->searchKpi($parameters);
    }

    /**
     * Get KPI Groups
     * 
     * @return Doctrine_Collection 
     */
    public function deleteKpi($ids) {
        foreach($ids as $id){
            $kpi = $this->getKpiById($id);
            if($kpi instanceof Kpi){
                $kpi->delete();
            }
        }
        return $this->getDao()->deleteKpi($ids);
    }

    /**
     * Search KPI by Jobtitle
     *
     * @param array $parameters
     * @return Doctrine_Collection
     */
    public function searchKpiByJobTitle($parameters = null) {
        return $this->getDao()->searchKpiByJobTitle($parameters);
    }

    public function getKpiCount($serachParams) {
        $kpiList = $this->getDao()->searchKpi($serachParams);
        return count($kpiList);
    }
    
    public function getKpiById($id){
        return $this->getDao()->getKpiById($id);
    }
    
    public function softDeleteKpi($ids) {
        foreach($ids as $id){
            $kpi = $this->getKpiById($id);
            if($kpi instanceof Kpi){
                $kpi->delete();
            }
        }
    }

}

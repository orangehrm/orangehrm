<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 *
 * Description of KpiSearchForm
 *
 * @author nadeera
 */

class KpiSearchForm extends BasePefromanceSearchForm {

    public function configure() {

        $this->setWidgets($this->getFormWidgets());
        $this->setValidators($this->getFormValidators());

        $this->getWidgetSchema()->setNameFormat('kpi360SearchForm[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());
    }

    /**
     *
     * @return array
     */
    public function getStylesheets() {
        $styleSheets = parent::getStylesheets();
        $styleSheets[plugin_web_path('orangehrmPerformancePlugin','css/kpiSearchSuccess.css')] = 'all';
        return $styleSheets;
    }

    /**
     *
     * @return array
     */
    protected function getFormWidgets() {
        $widgets = array(
            'jobTitleCode' => new sfWidgetFormChoice(array('choices' => $this->getJobTitleListAsArrayWithAllOption()), array('class' => 'formSelect'))
        );
        return $widgets;
    }

    /**
     *
     * @return array
     */
    protected function getFormValidators() {

        $validators = array(
            'jobTitleCode' => new sfValidatorString(array('required' => false))
        );
        return $validators;
    }

    /**
     *
     * @return array
     */
    protected function getFormLabels() {
        $labels = array(
            'jobTitleCode' => __('Job Title')
        );
        return $labels;
    }

    /**
     *
     * @return type 
     */
    public function searchKpi($page) {
        
        $serachParams ['jobCode'] =  $this->getValue('jobTitleCode');
        $serachParams ['page'] =  $page;
        $serachParams ['limit'] =  sfConfig::get('app_items_per_page');        

        return $this->getKpiService()->searchKpi($serachParams);
    }
    
    public function getKpiCount(){
        $serachParams ['jobCode'] =  $this->getValue('jobTitleCode');
        $serachParams['limit'] = null;
        
        return $this->getKpiService()->getKpiCount($serachParams);
    }
}
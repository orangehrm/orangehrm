<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 *
 * Description of DefineKpiForm
 *
 * @author nadeera
 */

class DefineKpiForm extends BasePefromanceSearchForm {

    
    public function configure() {

        $this->setWidgets($this->getFormWidgets());
        $this->setValidators($this->getFormValidators());

        $this->getWidgetSchema()->setNameFormat('defineKpi360[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());
    }

    /**
     *
     * @return array
     */
    public function getStylesheets() {
        $styleSheets = parent::getStylesheets();
        $styleSheets[plugin_web_path('orangehrmPerformancePlugin','css/defineKpiSuccess.css')] = 'all';
        return $styleSheets;
    }

    /**
     *
     * @return array 
     */
    public function getJobTitleListAsArray() {
        foreach ($this->getJobService()->getJobTitleList() as $job) {
            $jobTitles [$job->getId()] = $job->getJobTitleName();
        }
        return $jobTitles;
    }

    /**
     *
     * @return array
     */
    protected function getFormWidgets() {
        $widgets = array(
            'id' => new sfWidgetFormInputHidden(),
            'jobTitleCode' => new sfWidgetFormChoice(array('choices' => $this->getJobTitleListAsArrayWithSelectOption()), array('class' => 'formSelect')),  
            'keyPerformanceIndicators' => new sfWidgetFormInput(array(), array('class' => 'formInputText')),
            'minRating' => new sfWidgetFormInput(array(), array('class' => 'formInputText')),
            'maxRating' => new sfWidgetFormInput(array(), array('class' => 'formInputText')),
            'makeDefault' => new sfWidgetFormInputCheckbox(array(), array('class' => 'formCheckbox'))
                     
        );
        
        return $widgets;
    }

    /**
     *
     * @return array
     */
    protected function getFormValidators() {

        $validators = array(
            'id' => new sfValidatorString(array('required' => false)),
            'jobTitleCode' => new sfValidatorString(array('required' => false)),
            'keyPerformanceIndicators' => new sfValidatorString(array('required' => true)),
            'minRating' => new sfValidatorString(array('required' => false)),
            'maxRating' => new sfValidatorString(array('required' => false)),
            'makeDefault' => new sfValidatorString(array('required' => false))
        );
        return $validators;
    }

    /**
     *
     * @return array
     */
    protected function getFormLabels() {
       
        $requiredMarker = '&nbsp;<span class="required">*</span>';
        $labels = array(
            'jobTitleCode' => __('Job Title') . $requiredMarker,
            'keyPerformanceIndicators' => __('Key Performance Indicator') . $requiredMarker,
            'minRating' => __('Minimum Rating'). $requiredMarker,
            'maxRating' => __('Maximum Rating'). $requiredMarker,
            'makeDefault' => __('Make Default Scale')
        );
        return $labels;
    }

    public function saveForm() {
        $values = $this->getValues();
        $kpi = new Kpi();
        if ($values['id'] > 0) {
            $kpi = $this->getKpiService()->searchKpi(array('id' => $values['id']));
        }

        $kpi->setJobTitleCode($values['jobTitleCode']);
        $kpi->setKpiIndicators($values['keyPerformanceIndicators']);
        
        if( strlen( $values['minRating']) >0 ){
            $kpi->setMinRating($values['minRating']);
        }
        if($values['maxRating']){
           $kpi->setMaxRating($values['maxRating']); 
        }
        
        if ($values['makeDefault'] == 'on') {
            $kpi->setDefaultKpi(1);
        } else {
            $kpi->setDefaultKpi(null);
        }

        $this->getKpiService()->saveKpi($kpi);
    }

    /**
     *
     * @param integer $kpiId 
     */
    public function loadFormData($kpiId) {

        if ($kpiId > 0) {
            $kpi = $this->getKpiService()->searchKpi(array('id' => $kpiId));
            $this->setDefault('id', $kpi->getId());
            $this->setDefault('jobTitleCode', $kpi->getJobTitleCode());
            $this->setDefault('keyPerformanceIndicators', $kpi->getKpiIndicators());
            $this->setDefault('minRating', $kpi->getMinRating());
            $this->setDefault('maxRating', $kpi->getMaxRating());
            $this->setDefault('makeDefault', $kpi->getDefaultKpi());
            
        } else {
            
            $parameters ['isDefault'] = 1;
            $kpi = $this->getKpiService()->searchKpi($parameters);
            
            if(sizeof($kpi)>0){
                $kpi = $kpi->getFirst();
                $this->setDefault('minRating', $kpi->getMinRating());
                $this->setDefault('maxRating', $kpi->getMaxRating());
            }           
        }
    }
}
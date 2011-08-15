<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TaxExcemptions
 *
 * @author OrangeHRM
 */
class TaxExcemptions extends PIMPage{
    
    public $cmbfrderalITStatus;
    public $txtfederalITExcemptions;
    public $cmbstateITState;
    public $cmbstateITStatus;
    public $txtstateITExcemptions;
    public $cmbstateITUnempState;
    public $cmbstateITWorkState;
    public $btnSave;




    public function __construct($selenium) {
        parent::__construct($selenium);
        $this->cmbfrderalITStatus = "tax_federalStatus";
        $this->txtfederalITExcemptions = "tax_federalExemptions";
        $this->cmbstateITState = "tax_state";
        $this->cmbstateITStatus = "tax_stateStatus";
        $this->txtstateITExcemptions = "tax_stateExemptions";
        $this->cmbstateITUnempState = "tax_unempState";
        $this->cmbstateITWorkState = "tax_workState";
        $this->btnSave = "btnSave";
        
        }
        
        public function saveTaxDetails($FederalITStatus=null, $FederalITExemn=null, $StateITState=null, $stateITStatus=null, $stateITExcemptions=null, $stateITUnempState=null, $stateITWorkState=null){
            $this->selenium->click($this->btnSave);
            $this->selenium->select($this->cmbfrderalITStatus, $FederalITStatus);
            $this->selenium->type($this->txtfederalITExcemptions, $FederalITExemn);
            $this->selenium->select($this->cmbstateITState, $StateITState);
            $this->selenium->select($this->cmbstateITStatus, $stateITStatus);
            $this->selenium->type($this->txtstateITExcemptions, $stateITExcemptions);
            $this->selenium->select($this->cmbstateITUnempState, $stateITUnempState);
            $this->selenium->select($this->cmbstateITWorkState, $stateITWorkState);
            $this->selenium->click($this->btnSave);
            $this->selenium->waitForPageToLoad(Config::$timeoutValue);
            
            return new TaxExcemptions($this->selenium);
        }
        
         public function getSavedTaxExcemptionDetails(){
             
        $arrTaxExcemtionDetails = array();
        
        $this->arrTaxExcemtionDetails[0]= $this->selenium->getSelectedLabel($this->cmbfrderalITStatus);
        
        if($this->selenium->getValue($this->txtfederalITExcemptions)!= 0){
        $this->arrTaxExcemtionDetails[1]= $this->selenium->getValue($this->txtfederalITExcemptions);
        }else {
        $this->arrTaxExcemtionDetails[1] = null;
        }
         
        $this->arrTaxExcemtionDetails[2]= $this->selenium->getSelectedLabel($this->cmbstateITState);
        $this->arrTaxExcemtionDetails[3]= $this->selenium->getSelectedLabel($this->cmbstateITStatus);
        
        if($this->selenium->getValue($this->txtstateITExcemptions) != 0){
        $this->arrTaxExcemtionDetails[4]= $this->selenium->getValue($this->txtstateITExcemptions);
        }else{
        $this->arrTaxExcemtionDetails[4] = null;
        }
        
        $this->arrTaxExcemtionDetails[5]= $this->selenium->getSelectedLabel($this->cmbstateITUnempState);
        $this->arrTaxExcemtionDetails[6]= $this->selenium->getSelectedLabel($this->cmbstateITWorkState);
        
       
        return $this->arrTaxExcemtionDetails;
         
      } 
    
}

?>

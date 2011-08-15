<?php



class AddSupervisorSubordinateComponent extends Component {
    
    public $rdbtnSupervisor;
    public $rdbtnSubordinate;
    public $txtReportto_name;
    public $cmbReportingMethodType;
    public $txtRreportingMethod;
    public $btnSaveReportTo;
    
    public function __construct(PHPUnit_Extensions_SeleniumTestCase $selenium){
        
        parent::__construct($selenium,"Add Supervisor/Subordinate");
           
        $this->rdbtnSupervisor = "reportto_type_flag_1";
        $this->rdbtnSubordinate = "reportto_type_flag_2";
        $this->txtReportto_name = "reportto_name";
        $this->cmbReportingMethodType = "reportto_reportingMethodType";
        $this->txtRreportingMethod = "reportto_reportingMethod";
        $this->btnSaveReportTo = "btnSaveReportTo";
        
        
    }



//put your code here
}

?>

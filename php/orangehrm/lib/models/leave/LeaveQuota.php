<?
class LeaveQuota {
	
	private $leaveTypeId;
	private $leaveTypeNameId;
	private $employeeId;
	private $noOfDaysAllotted;
	private $leaveTypeName;
	
	public function __construct() {
		
	}
	
	public function getLeaveTypeId() {
		return $this->leaveTypeId;
	}

	public function setLeaveTypeId($leaveTypeId) {
		$this->leaveTypeId = $leaveTypeId;
	}
	
	public function getLeaveTypeNameId() {
		return $this->leaveTypeNameId;
	}

	public function setLeaveTypeNameId($leaveTypeNameId) {
		$this->leaveTypeNameId = $leaveTypeNameId;
	}
	
	public function getEmployeeId() {
		return $this->employeeId;
	}

	public function setEmployeeId($employeeId) {
		$this->employeeId = $employeeId;
	}
	
	public function getNoOfDaysAllotted() {
		return $this->noOfDaysAllotted;
	}

	public function setNoOfDaysAllotted($noOfDaysAlotted) {
		$this->noOfDaysAllotted = $noOfDaysAlotted;
	}
	
	public function getLeaveTypeName() {
		return $this->leaveTypeName;
	}

	public function setLeaveTypeName($leaveTypeName) {
		$this->leaveTypeName = $leaveTypeName;
	}
	
	public function addLeaveQuota() {
		
	}
	
	public function editLeaveQuota() {
		
	}
	
	public function deleteLeaveQuota() {
		
	}

	public function fetchLeaveQuota($eployeeId) {
		$tmpObj = new LeaveQuota();
		
		$tmpObj->setLeaveTypeId("LTY011");
		$tmpObj->setLeaveTypeName("Medical");
		
		$leaveTypeArr[] = $tmpObj;
		
		$tmpObj = new LeaveQuota();
		
		$tmpObj->setLeaveTypeId("LTY012");
		$tmpObj->setLeaveTypeName("Casual");
		
		$leaveTypeArr[] = $tmpObj;
		
		return $leaveTypeArr;
	}
}
?>
<?php
class LeaveStateManager implements LeaveStateManagerInterface {

	private $leavePendingAprrovalState;
	private $leaveApprovedState;
	private $leaverejectedState;
	private $leaveCancelledState;
	private $leaveTakenState;
	private $leaveWeekendState;
	private $leaveHolidayState;
	private $state;
	private $leave;
	private $changeComments;
	private $leaveEntitlementService;
	private static $instance = null;

	private function __construct() {
		$this->leavePendingAprrovalState = new LeavePendingApprovalState($this);
		$this->leaveApprovedState = new LeaveApprovedState($this);
		$this->leaverejectedState = new LeaveRejectedState($this);
		$this->leaveCancelledState = new LeaveCancelledState($this);
		$this->leaveTakenState = new LeaveTakenState($this);
		$this->leaveWeekendState = new LeaveWeekendState($this);
		$this->leaveHolidayState = new LeaveHolidayState($this);
		$this->state = null;
		$this->leave = null;
		$this->changeComments = null;
		$this->leaveEntitlementService = new LeaveEntitlementService();
	}
	
	public function instance() {

		if (!(self::$instance instanceof LeaveStateManager)) {
			self::$instance = new LeaveStateManager();
		}

		return self::$instance;

	}

	public function approve() {
		$this->setState($this->getStateObjectByValue($this->leave->getLeaveStatus()));

		$this->_checkPrerequisits();

		$leaveDateTimestamp = strtotime($this->leave->getLeaveDate());
		$currentTimestamp = strtotime(date('Y-m-d'), true);
		
		try {
			$this->state->approve();
			if ($leaveDateTimestamp <= $currentTimestamp) {
				$this->state->take();
			}
		} catch (Exception $e) {
			
		}

		$this->leave->setLeaveStatus($this->state->getStateValue());
		$this->leave->setLeaveComments($this->changeComments);

		$result = $this->leave->save();
		
		if ($leaveDateTimestamp <= $currentTimestamp) {
			$this->adjustLeaveEntitlement($this->leave, 1);
		}
		
		return $result;
	}

	public function reject() {
		$this->setState($this->getStateObjectByValue($this->leave->getLeaveStatus()));

		$this->_checkPrerequisits();

		try {
			$this->state->reject();
		} catch (Exception $e) {
			
		}

		$this->leave->setLeaveStatus($this->state->getStateValue());
		$this->leave->setLeaveComments($this->changeComments);

		return $this->leave->save();
	}

	public function cancel() {
		$existingStatus	=	$this->leave->getLeaveStatus();
		$this->setState($this->getStateObjectByValue($this->leave->getLeaveStatus()));

		$this->_checkPrerequisits();

		try {
			$this->state->cancel();
		} catch (Exception $e) {
			
		}

		$this->leave->setLeaveStatus($this->state->getStateValue());

		$this->leave->save();
		
		
		if($existingStatus == Leave::LEAVE_STATUS_LEAVE_TAKEN ){
			$this->adjustLeaveEntitlement($this->leave, -($this->leave->getLeaveLengthDays()));
		}
		 
		 return true;
	}

	public function getLeavePendingApprovalState(){
		return $this->leavePendingAprrovalState;
	}

	public function getLeaveApprovedState() {
		return $this->leaveApprovedState;
	}

	public function getLeaveRejectedState() {
		return $this->leaverejectedState;
	}

	public function getLeaveCancelledState() {
		return $this->leaveCancelledState;
	}

	public function getLeaveTakenState() {
		return $this->leaveTakenState;
	}

	public function getLeaveWeekendState() {
		return $this->leaveWeekendState;
	}
	
	public function getLeaveHolidayState() {
		return $this->leaveHolidayState;
	}
	
	
	public function setState(LeaveState $state) {
		$this->state = $state;


	}

	public function setLeave(Leave $leave) {
		$this->leave = $leave;
	}

	private function _checkPrerequisits() {
		if (is_null($this->state)) {
			throw new Exception('State is not set');
		}

		if (is_null($this->leave)) {
			throw new Exception('Leave object is not set');
		}
	}

	public function getStateObjectByValue($stateValue) {
		switch ($stateValue) {
			case Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL:
				return $this->getLeavePendingApprovalState();
				break;
			case Leave::LEAVE_STATUS_LEAVE_APPROVED:
				return $this->getLeaveApprovedState();
				break;
			case Leave::LEAVE_STATUS_LEAVE_REJECTED:
				return $this->getLeaveRejectedState();
				break;
			case Leave::LEAVE_STATUS_LEAVE_CANCELLED:
				return $this->getLeaveCancelledState();
				break;
			case Leave::LEAVE_STATUS_LEAVE_TAKEN:
				return $this->getLeaveTakenState();
				break;
			case Leave::LEAVE_STATUS_LEAVE_WEEKEND:
				return $this->getLeaveWeekendState();
				break;
			case Leave::LEAVE_STATUS_LEAVE_HOLIDAY:
				return $this->getLeaveHolidayState();
				break;
			default:
				return null;
				break;
		}
	}
	
	public function adjustLeaveEntitlement($leave, $adjustment) {
		return $this->leaveEntitlementService->adjustEmployeeLeaveEntitlement($leave, $adjustment);
	}
	
	public function setChangeComments($comments) {
		$this->changeComments = $comments;
	}

}
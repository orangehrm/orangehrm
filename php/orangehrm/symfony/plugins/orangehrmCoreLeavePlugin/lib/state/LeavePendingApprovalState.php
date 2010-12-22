<?php
class LeavePendingApprovalState implements LeaveState {
	
	private $leaveStateManger;
	
	public function __construct(LeaveStateManagerInterface $leaveStateManger) {
		$this->leaveStateManger = $leaveStateManger;
	}

	public function approve() {
		$this->leaveStateManger->setState($this->leaveStateManger->getLeaveApprovedState());
	}
	
	public function reject() {
		$this->leaveStateManger->setState($this->leaveStateManger->getLeaveRejectedState());
	}
	
	public function cancel() {
		$this->leaveStateManger->setState($this->leaveStateManger->getLeaveCancelledState());
	}
	
	public function take() {
		$this->leaveStateManger->setState($this->leaveStateManger->getLeaveTakenState());
	}
	
	public function getStateValue() {
		return Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL;
	}
}
<?php
class LeaveApprovedState implements LeaveState {
	
	private $leaveStateManger;
	
	public function __construct(LeaveStateManagerInterface $leaveStateManger) {
		$this->leaveStateManger = $leaveStateManger;
	}

	public function approve() {
		throw new Exception('Leave is already approved');
	}
	
	public function reject() {
		throw new Exception('Leave is already approved');
	}
	
	public function cancel() {
		$this->leaveStateManger->setState($this->leaveStateManger->getLeaveCancelledState());
	}
	
	public function take() {
		$this->leaveStateManger->setState($this->leaveStateManger->getLeaveTakenState());
	}
	
	public function getStateValue() {
		return Leave::LEAVE_STATUS_LEAVE_APPROVED;
	}
}
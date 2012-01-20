<?php
class LeaveTakenState implements LeaveState {
	
	private $leaveStateManger;
	
	public function __construct(LeaveStateManagerInterface $leaveStateManger) {
		$this->leaveStateManger = $leaveStateManger;
	}

	public function approve() {
		throw new Exception('Leave is already taken');
	}
	
	public function reject() {
		throw new Exception('Leave is already taken');
	}
	
	public function cancel() {
		$this->leaveStateManger->setState($this->leaveStateManger->getLeaveCancelledState());
	}
	
	public function take() {
		throw new Exception('Leave is already taken');
	}
	
	public function getStateValue() {
		return Leave::LEAVE_STATUS_LEAVE_TAKEN;
	}
}
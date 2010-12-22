<?php
class LeaveCancelledState implements LeaveState {
	
	private $leaveStateManger;
	
	public function __construct(LeaveStateManagerInterface $leaveStateManger) {
		$this->leaveStateManger = $leaveStateManger;
	}

	public function approve() {
		throw new Exception('Leave is already cencelled');
	}
	
	public function reject() {
		throw new Exception('Leave is already cencelled');
	}
	
	public function cancel() {
		throw new Exception('Leave is already cencelled');
	}
	
	public function take() {
		throw new Exception('Leave is already cencelled');
	}
	
	public function getStateValue() {
		return Leave::LEAVE_STATUS_LEAVE_CANCELLED;
	}
}
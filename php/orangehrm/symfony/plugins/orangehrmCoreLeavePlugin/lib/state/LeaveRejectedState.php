<?php
class LeaveRejectedState implements LeaveState {
	
	private $leaveStateManger;
	
	public function __construct(LeaveStateManagerInterface $leaveStateManger) {
		$this->leaveStateManger = $leaveStateManger;
	}

	public function approve() {
		throw new Exception('Leave is already rejected');
	}
	
	public function reject() {
		throw new Exception('Leave is already rejected');
	}
	
	public function cancel() {
		throw new Exception('Leave is already rejected');
	}
	
	public function take() {
		throw new Exception('Leave is already rejected');
	}
	
	public function getStateValue() {
		return Leave::LEAVE_STATUS_LEAVE_REJECTED;
	}
}
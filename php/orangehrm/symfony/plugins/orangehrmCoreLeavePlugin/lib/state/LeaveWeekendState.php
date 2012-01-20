<?php
class LeaveWeekendState implements LeaveState {
	
	private $leaveStateManger;
	
	public function __construct(LeaveStateManagerInterface $leaveStateManger) {
		$this->leaveStateManger = $leaveStateManger;
	}

	public function approve() {
		
	}
	
	public function reject() {
		
	}
	
	public function cancel() {
		
	}
	
	public function take() {
		
	}
	
	public function getStateValue() {
		return Leave::LEAVE_STATUS_LEAVE_WEEKEND;
	}
}
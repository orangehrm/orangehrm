<?php
interface LeaveStateManagerInterface {

	public function approve();
	public function reject();
	public function cancel();
	public function setState(LeaveState $state);
	public function getLeavePendingApprovalState();
	public function getLeaveApprovedState();
	public function getLeaveRejectedState();
	public function getLeaveCancelledState();
	public function getLeaveTakenState();

}
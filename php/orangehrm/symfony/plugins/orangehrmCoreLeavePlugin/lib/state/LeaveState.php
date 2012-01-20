<?php
interface LeaveState {
	
	public function approve();
	public function reject();
	public function cancel();
	public function take();
	public function getStateValue();
}
<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */

/**
 * Class containing details of time spent on an activity
 */
 class ProjectActivityTime {

	/**
	 * Class Attributes
	 */
	protected $activityId;
	protected $activityName;
	protected $projectId;

	/** Time spent on activity, in seconds */
	protected $activityTime;

	public function setActivityId($activityId) {
		$this->activityId = $activityId;
	}

	public function getActivityId() {
		return $this->activityId;
	}

	public function setActivityName($activityName) {
		$this->activityName = $activityName;
	}

	public function getActivityName() {
		return $this->activityName;
	}

	public function setActivityTime($activityTime) {
		$this->activityTime = $activityTime;
	}

	public function getActivityTime() {
		return $this->activityTime;
	}

	public function setProjectId($projectId) {
		$this->projectId = $projectId;
	}

	public function getProjectId() {
		return $this->projectId;
	}

	/**
	 * Constructor
	 *
	 * @param int    $activityId Activity ID
	 * @param string $activityName Activity Name
	 * @param int    $activityTime The time spent on the activity (in seconds)
	 * @param int    $projectId The project to which this activity belongs
	 */
	public function __construct($activityId, $activityName, $activityTime, $projectId) {
		$this->activityId = $activityId;
		$this->activityName = $activityName;
		$this->activityTime = $activityTime;
		$this->projectId = $projectId;
	}

 }

?>

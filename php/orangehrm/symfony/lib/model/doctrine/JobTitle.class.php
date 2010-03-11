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
 */
/**
 * JobTitle Class
 *
 */
class JobTitle extends BaseJobTitle
{
	public function __toString() {
		return $this->name;
	}
	
	/**
	 * List all kpi defined Job Titles
	 * @return $jobTitlesArr
	 */
	public function getJobTitlesDefined(){
		$q = new Doctrine_RawSql();
		$q->select('{jt.jobtit_name}')
		->from('hs_hr_kpi jk')
		->leftjoin('hs_hr_job_title jt ON jt.jobtit_code = jk.job_title_code')
		->addComponent('jt', 'JobTitle');
		$query = $q->execute();
		$toArray = $query->toArray();
		if ($query->toArray()) {
			for($i=0; $i<count($toArray); $i++){
				$jobTitles[] = array($toArray[$i]['id'] => $toArray[$i]['name']);
			}
			$jobTitlesArr = $jobTitles;
		} else {
			$jobTitlesArr = array();
		}
		return $jobTitlesArr;
	}
}
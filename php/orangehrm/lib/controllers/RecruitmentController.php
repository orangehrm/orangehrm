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

require_once ROOT_PATH . '/lib/exception/ExceptionHandler.php';
require_once ROOT_PATH . '/lib/common/FormCreator.php';
require_once ROOT_PATH . '/lib/common/authorize.php';
require_once ROOT_PATH . '/lib/common/TemplateMerger.php';

require_once ROOT_PATH . '/lib/models/maintenance/UserGroups.php';
require_once ROOT_PATH . '/lib/models/maintenance/Users.php';
require_once ROOT_PATH . '/lib/models/maintenance/Rights.php';
require_once ROOT_PATH . '/lib/models/eimadmin/CountryInfo.php';
require_once ROOT_PATH . '/lib/models/eimadmin/ProvinceInfo.php';
require_once ROOT_PATH . '/lib/models/recruitment/JobVacancy.php';
require_once ROOT_PATH . '/lib/models/recruitment/JobApplication.php';
require_once ROOT_PATH . '/lib/models/recruitment/RecruitmentMailNotifier.php';
require_once ROOT_PATH . '/lib/extractor/recruitment/EXTRACTOR_ViewList.php';
require_once ROOT_PATH . '/lib/extractor/recruitment/EXTRACTOR_JobVacancy.php';
require_once ROOT_PATH . '/lib/extractor/recruitment/EXTRACTOR_JobApplication.php';


/**
 * Controller for recruitment module
 */
class RecruitmentController {

	private $authorizeObj;

    /**
     * Constructor
     */
    public function __construct() {
        if (isset($_SESSION) && isset($_SESSION['fname']) ) {
			$this->authorizeObj = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);
        }
    }

    /**
     * Handle incoming requests
     * @param String code Recruit code
     */
    public function handleRequest($code) {

		if (empty($code) || !isset($_GET['action'])) {
			trigger_error("Invalid Action " . $_GET['action'], E_USER_NOTICE);
			return;
		}

		switch ($code) {

			case 'Vacancy' :
				$viewListExtractor = new EXTRACTOR_ViewList();

	            switch ($_GET['action']) {

	                case 'List' :
	                	$searchObject = $viewListExtractor->parseSearchData($_POST, $_GET);
	                    $this->_viewVacancies($searchObject);
	                    break;

	                case 'View' :
	                	$id = isset($_GET['id'])? $_GET['id'] : null;
	                	$this->_viewVacancy($id);
						break;

	                case 'ViewAdd' :
	                	$this->_viewAddVacancy();
	                	break;

					case 'Add' :
						$extractor = new EXTRACTOR_JobVacancy();
						$vacancy = $extractor->parseData($_POST);
						$this->_addVacancy($vacancy);
						break;

					case 'Update' :
						$extractor = new EXTRACTOR_JobVacancy();
						$vacancy = $extractor->parseData($_POST);
						$this->_updateVacancy($vacancy);
						break;

	                case 'Delete' :
	                    $ids = $_POST['chkID'];
	                    $this->_deleteVacancies($ids);
	                	break;
	            }
	            break;
	    }
    }

	/**
	 * Generic method to display a list
	 * @param int $pageNumber Page Number
	 * @param int $count Total number of results
	 * @param Array $list results (in current page)
	 */
	private function _viewList($pageNumber, $count, $list) {

        $formCreator = new FormCreator($_GET, $_POST);
        $formCreator->formPath = '/recruitmentview.php';
        $formCreator->popArr['currentPage'] = $pageNumber;
        $formCreator->popArr['list'] = $list;
        $formCreator->popArr['count'] = $count;
        $formCreator->display();
	}

	/**
	 * View list of vacancies
	 * @param SearchObject Object with search parameters
	 */
    private function _viewVacancies($searchObject) {

		if ($this->authorizeObj->isAdmin()) {
        	$list = JobVacancy::getListForView($searchObject->getPageNumber(), $searchObject->getSearchString(), $searchObject->getSearchField(), $searchObject->getSortField(), $searchObject->getSortOrder());
        	$count = Jobvacancy::getCount($searchObject->getSearchString(), $searchObject->getSearchField());
        	$this->_viewList($searchObject->getPageNumber(), $count, $list);
		} else {
		    trigger_error("Not Authorized!", E_USER_NOTICE);
		}
    }

	/**
	 * Delete vacancies with given IDs
	 * @param Array $ids Array with Vacancy ID's to delete
	 */
    private function _deleteVacancies($ids) {
		if ($this->authorizeObj->isAdmin()) {
			try {
        		$count = JobVacancy::delete($ids);
        		$message = 'DELETE_SUCCESS';
			} catch (JobVacancyException $e) {
				$message = 'DELETE_FAILURE';
			}
            $this->redirect($message, '?recruitcode=Vacancy&action=List');
		} else {
		    trigger_error("Not Authorized!", E_USER_NOTICE);
		}
    }

	/**
	 * View add Vacancy page
	 */
	private function _viewAddVacancy() {
		if ($this->authorizeObj->isAdmin()) {
	    	$this->_viewVacancy();
	    } else {
		    trigger_error("Not Authorized!", E_USER_NOTICE);
		}
	}

    /**
     * View vacancy
     * @param int $id Id of vacancy. If empty, A new vacancy is shown
     */
    private function _viewVacancy($id = null) {

		$path = '/templates/recruitment/jobVacancy.php';

		try {
			if (empty($id)) {
				$vacancy = new JobVacancy();
			} else {
				$vacancy = JobVacancy::getJobVacancy($id);
			}

			$empInfo = new EmpInfo;
			$managers = $empInfo->getListofEmployee(0, 'Manager', 6);
			$jobTitle = new JobTitle();
			$jobTitles = $jobTitle->getJobTit();

			$objs['vacancy'] = $vacancy;
			$objs['managers'] = is_array($managers) ? $managers : array();
			$objs['jobTitles'] = is_array($jobTitles) ? $jobTitles : array();

			$template = new TemplateMerger($objs, $path);
			$template->display();
		} catch (JobVacancyException $e) {
			$message = 'UNKNOWN_FAILURE';
            $this->redirect($message);
		}
    }

    /**
     * Add vacancy to database
     * @param JobVacancy $vacancy Job Vacancy object to add
     */
    private function _addVacancy($vacancy) {
		if ($this->authorizeObj->isAdmin()) {
			try {
				$vacancy->save();
	        	$message = 'ADD_SUCCESS';
	        	$this->redirect($message, '?recruitcode=Vacancy&action=List');
			} catch (JobVacancyException $e) {
				$message = 'ADD_FAILURE';
	        	$this->redirect($message);
			}
		} else {
		    trigger_error("Not Authorized!", E_USER_NOTICE);
		}

    }

    /**
     * Add vacancy to database
     * @param JobVacancy $vacancy Job Vacancy object to add
     */
    private function _updateVacancy($vacancy) {
		if ($this->authorizeObj->isAdmin()) {
			try {
				$vacancy->save();
	        	$message = 'UPDATE_SUCCESS';
	        	$this->redirect($message, '?recruitcode=Vacancy&action=List');
			} catch (JobVacancyException $e) {
				$message = 'UPDATE_FAILURE';
	        	$this->redirect($message);
			}
		} else {
		    trigger_error("Not Authorized!", E_USER_NOTICE);
		}
    }

	/**
	 * Shows a list of active job vacancies to job applicant.
	 */
	public function showVacanciesToApplicant() {
		$path = '/templates/recruitment/applicant/viewVacancies.php';
		$objs['vacancies'] = JobVacancy::getActive();
		$template = new TemplateMerger($objs, $path);
		$template->display();
	}

	/**
	 * Display job application form to applicant
	 *
	 * @param int $id Job Vacancy ID
	 */
	public function showJobApplication($id) {
		$path = '/templates/recruitment/applicant/viewJobApplication.php';

		$objs['vacancy'] = JobVacancy::getJobVacancy($id);

		$countryinfo = new CountryInfo();
		$objs['countryList'] = $countryinfo->getCountryCodes();

		$template = new TemplateMerger($objs, $path);
		$template->display();
	}

	/**
	 * Handle job application by applicant
	 */
	public function applyForJob() {
		$extractor = new EXTRACTOR_JobApplication();
		$jobApplication = $extractor->parseData($_POST);
		try {
		    $jobApplication->save();
		    $result = true;
		} catch (JobApplicationException $e) {
			$result = false;
		}

		// Send mail notifications
		$notifier = new RecruitmentMailNotifier();
		$notifier->sendApplicationReceivedEmailToManager($jobApplication);

		// We only need to display result of email sent to applicant
		$mailResult = $notifier->sendApplicationReceivedEmailToApplicant($jobApplication);

		$path = '/templates/recruitment/applicant/jobApplicationStatus.php';
		$objs['application'] = $jobApplication;
		$objs['vacancy'] = JobVacancy::getJobVacancy($jobApplication->getVacancyId());
		$objs['result'] = $result;
		$objs['mailResult'] = $mailResult;
		$template = new TemplateMerger($objs, $path);
		$template->display();
	}

	/**
	 * Return the province codes for the given country.
	 * Used by xajax calls.
	 * @param String $countryCode The country code
	 * @return Array 2D Array of Province Codes and Province Names
	 */
	public static function getProvinceList($countryCode) {
		$province = new ProvinceInfo();
		return $province->getProvinceCodes($countryCode);
	}

	/**
	 * Redirect to given url or current page while displaying optional message
	 *
	 * @param String $message Message to display
	 * @param String $url URL
	 */
	public function redirect($message=null, $url = null) {

		if (isset($url)) {
			$mes = "";
			if (isset($message)) {
				$mes = "&message=";
			}
			$url=array($url.$mes);
			$id="";
		} else if (isset($message)) {
			preg_replace('/[&|?]+id=[A-Za-z0-9]*/', "", $_SERVER['HTTP_REFERER']);

			if (preg_match('/&/', $_SERVER['HTTP_REFERER']) > 0) {
				$message = "&message=".$message;
				$url = preg_split('/(&||\?)message=[A-Za-z0-9]*/', $_SERVER['HTTP_REFERER']);
			} else {
				$message = "?message=".$message;
			}

			if (isset($_REQUEST['id']) && !empty($_REQUEST['id']) && !is_array($_REQUEST['id'])) {
				$id = "&id=".$_REQUEST['id'];
			} else {
				$id="";
			}
		} else {
			if (isset($_REQUEST['id']) && !empty($_REQUEST['id']) && (preg_match('/&/', $_SERVER['HTTP_REFERER']) > 0)) {
				$id = "&id=".$_REQUEST['id'];
			} else if (preg_match('/&/', $_SERVER['HTTP_REFERER']) == 0){
				$id = "?id=".$_REQUEST['id'];
			} else {
				$id="";
			}
		}

		header("Location: ".$url[0].$message.$id);
	}

}
?>

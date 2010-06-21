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
require_once ROOT_PATH . '/lib/common/AjaxCalls.php';

require_once ROOT_PATH . '/lib/models/maintenance/UserGroups.php';
require_once ROOT_PATH . '/lib/models/maintenance/Users.php';
require_once ROOT_PATH . '/lib/models/maintenance/Rights.php';
require_once ROOT_PATH . '/lib/models/eimadmin/CountryInfo.php';
require_once ROOT_PATH . '/lib/models/eimadmin/ProvinceInfo.php';
require_once ROOT_PATH . '/lib/models/eimadmin/JobTitle.php';
require_once ROOT_PATH . '/lib/models/eimadmin/GenInfo.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpInfo.php';
require_once ROOT_PATH . '/lib/models/recruitment/JobVacancy.php';
require_once ROOT_PATH . '/lib/models/recruitment/JobApplication.php';
require_once ROOT_PATH . '/lib/models/recruitment/JobApplicationEvent.php';
require_once ROOT_PATH . '/lib/models/recruitment/RecruitmentMailNotifier.php';
require_once ROOT_PATH . '/lib/models/recruitment/RecruitmentAuthManager.php';
require_once ROOT_PATH . '/lib/extractor/recruitment/EXTRACTOR_ViewList.php';
require_once ROOT_PATH . '/lib/extractor/recruitment/EXTRACTOR_JobVacancy.php';
require_once ROOT_PATH . '/lib/extractor/recruitment/EXTRACTOR_JobApplication.php';
require_once ROOT_PATH . '/lib/extractor/recruitment/EXTRACTOR_JobApplicationEvent.php';
require_once ROOT_PATH . '/lib/extractor/recruitment/EXTRACTOR_ScheduleInterview.php';

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

            case 'Application' :
                $id = isset($_GET['id']) ? $_GET['id'] : null;

                switch ($_GET['action']) {

                    case 'List' :
                        $this->_viewApplicationList();
                        break;
                    case 'ConfirmReject' :
                        $this->_confirmAction($id, JobApplication::ACTION_REJECT);
                        break;
                    case 'Reject' :
                        $eventExtractor = new EXTRACTOR_JobApplicationEvent();
                        $event = $eventExtractor->parseAddData($_POST);
                        $this->_rejectApplication($event);
                        break;
                    case 'ConfirmFirstInterview' :
                        $this->_scheduleFirstInterview($id);
                        break;
                    case 'FirstInterview' :
                        $interviewExtractor = new EXTRACTOR_ScheduleInterview();
                        $event = $interviewExtractor->parseAddData($_POST);
                        $this->_saveFirstInterview($event);
                        break;
                    case 'ConfirmSecondInterview' :
                        $this->_scheduleSecondInterview($id);
                        break;
                    case 'SecondInterview' :
                        $interviewExtractor = new EXTRACTOR_ScheduleInterview();
                        $event = $interviewExtractor->parseAddData($_POST);
                        $this->_saveSecondInterview($event);
                        break;
                    case 'ConfirmOfferJob' :
                        $this->_confirmAction($id, JobApplication::ACTION_OFFER_JOB);
                        break;
                    case 'OfferJob' :
                        $eventExtractor = new EXTRACTOR_JobApplicationEvent();
                        $event = $eventExtractor->parseAddData($_POST);
                        $this->_offerJob($event);
                        break;
                    case 'ConfirmMarkDeclined' :
                        $this->_confirmAction($id, JobApplication::ACTION_MARK_OFFER_DECLINED);
                        break;
                    case 'MarkDeclined' :
                        $eventExtractor = new EXTRACTOR_JobApplicationEvent();
                        $event = $eventExtractor->parseAddData($_POST);
                        $this->_markDeclined($event);
                        break;
                    case 'ConfirmSeekApproval' :
                        $this->_confirmSeekApproval($id);
                        break;
                    case 'SeekApproval' :
                        $eventExtractor = new EXTRACTOR_JobApplicationEvent();
                        $event = $eventExtractor->parseSeekApprovalData($_POST);
                        $this->_seekApproval($event);
                        break;
                    case 'ConfirmApprove' :
                        $this->_confirmAction($id, JobApplication::ACTION_APPROVE);
                        break;
                    case 'Approve' :
                        $eventExtractor = new EXTRACTOR_JobApplicationEvent();
                        $event = $eventExtractor->parseAddData($_POST);
                        $this->_approve($event);
                        break;
                    case 'ViewDetails' :
                        $this->_viewApplicationDetails($id);
                        break;
                    case 'ViewHistory' :
                        $this->_viewApplicationHistory($id);
                        break;
                    case 'EditEvent' :
                        $eventExtractor = new EXTRACTOR_JobApplicationEvent();
                        $object = $eventExtractor->parseUpdateData($_POST);
                        $this->_editEvent($object);
                        break;
                    case 'downloadResume' :
                    	JobApplication::downloadResume($_GET['id']);
                    	break;
                    case 'deleteResume' :
                    	$this->_deleteResume($_GET['id']);
                    	break;
                    case 'replaceResume' :
                    	$this->_replaceResume();
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
      $screenParam = array('recruitcode' => $_GET['recruitcode'], 'action' => $_GET['action']);
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

        $formCreator = new FormCreator($_GET, $_POST);
        $formCreator->formPath = '/recruitmentview.php';
        $formCreator->popArr['currentPage'] = $pageNumber;
        $formCreator->popArr['list'] = $list;
        $formCreator->popArr['count'] = $count;
        $formCreator->popArr['token'] = $token;
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
            $this->_notAuthorized();
		}
    }

	/**
	 * Delete vacancies with given IDs
	 * @param Array $ids Array with Vacancy ID's to delete
	 */
    private function _deleteVacancies($ids) {
      $screenParam = array('recruitcode' => $_GET['recruitcode'], 'action' => 'List');
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

		if ($this->authorizeObj->isAdmin()) {
			try {
            $message = 'DELETE_FAILURE';
            if($token == $_POST['token']) {
               $count = JobVacancy::delete($ids);
               $message = 'DELETE_SUCCESS';
            }
			} catch (JobVacancyException $e) {
				$message = 'DELETE_FAILURE';
			}
            $this->redirect($message, '?recruitcode=Vacancy&action=List');
		} else {
            $this->_notAuthorized();
		}
    }

	/**
	 * View add Vacancy page
	 */
	private function _viewAddVacancy() {
		if ($this->authorizeObj->isAdmin()) {
	    	$this->_viewVacancy();
	    } else {
            $this->_notAuthorized();
		}
	}

    /**
     * View vacancy
     * @param int $id Id of vacancy. If empty, A new vacancy is shown
     */
    private function _viewVacancy($id = null) {
		$path = '/templates/recruitment/jobVacancy.php';

      $screenParam = array('recruitcode' => $_GET['recruitcode'], 'action' => $_GET['action'], 'id' => $id);
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

		try {
			if (empty($id)) {
				$vacancy = new JobVacancy();
			} else {
				$vacancy = JobVacancy::getJobVacancy($id);
			}

			$empInfo = new EmpInfo;
			$jobTitle = new JobTitle();
			$jobTitles = $jobTitle->getJobTit();

			$objs['vacancy'] = $vacancy;
			$objs['manager'] = $vacancy->getManagerName();
			$objs['noOfEmployees'] = $empInfo->countEmployee();
			$objs['employeeSearchList'] = $this->_getEmployeeSearchList();
			$objs['jobTitles'] = is_array($jobTitles) ? $jobTitles : array();
         $objs['token'] = $token;
         
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
      $screenParam = array('recruitcode' => $_GET['recruitcode'], 'action' => 'ViewAdd', 'id' => $_POST['txtId']);
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
      
		if ($this->authorizeObj->isAdmin()) {
			try {
            $message = 'ADD_FAILURE';
            if($_POST['token'] == $token) {
               $vacancy->save();
               $message = 'ADD_SUCCESS';
               $this->redirect($message, '?recruitcode=Vacancy&action=List');
            } else {
               $this->redirect($message);
            }
			} catch (JobVacancyException $e) {
				$message = 'ADD_FAILURE';
	        	$this->redirect($message);
			}
		} else {
            $this->_notAuthorized();
		}

    }

    /**
     * Add vacancy to database
     * @param JobVacancy $vacancy Job Vacancy object to add
     */
    private function _updateVacancy($vacancy) {

      $screenParam = array('recruitcode' => $_GET['recruitcode'], 'action' => 'View', 'id' => $_POST['txtId']);
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
      
		if ($this->authorizeObj->isAdmin()) {
			try {
            $message = 'UPDATE_FAILURE';
            if($token == $_POST['token']) {
               $vacancy->save();
               $message = 'UPDATE_SUCCESS';
               $this->redirect($message, '?recruitcode=Vacancy&action=List');
            } else {
               $this->redirect($message);
            }
			} catch (JobVacancyException $e) {
				$message = 'UPDATE_FAILURE';
	        	$this->redirect($message);
			}
		} else {
            $this->_notAuthorized();
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

		$genInfo = new GenInfo();
		$objs['company'] = $genInfo->getValue('COMPANY');

		$objs['retrySubmission'] = false;

		$extractor = new EXTRACTOR_JobApplication();
		if (isset($_GET['retrySubmission']) && $_GET['retrySubmission'] == 1) {
			$objs['savedData'] = $extractor->retrieveFromSession();
			$objs['retrySubmission'] = true;
		} else {
			$extractor->removeFromSession();
		}

		$template = new TemplateMerger($objs, $path);
		$template->display();
	}

	/**
	 * Handle job application by applicant
	 */
	public function applyForJob() {

		$extractor = new EXTRACTOR_JobApplication();
		$jobApplication = $extractor->parseData($_POST);

		$objs['error']['resumeUploadError'] = '';
		$objs['error']['resumeCompatibleError'] = '';
		$objs['error']['applicantEmailError'] = '';
		$objs['savingStatus'] = null;
		$correctResume = null;

		/* Handling uploaded resume */

		if (!empty($jobApplication->resumeData['error'])) { // If there was an error in upload, don't execute the rest

		    $correctResume = false;
		    $objs['error']['resumeUploadError'] = $jobApplication->resumeData['error'];
		    $extractor->saveToSession($_POST);

		} else { // This means resume has either successfully uploaded or no resume has been uploaded

		    if ($jobApplication->resumeData['size'] > 0) { // If a resume has been uploaded

				if ($jobApplication->isResumeCompatible()) {
					$correctResume = true;
				} else {
					$correctResume = false;
					$objs['error']['resumeCompatibleError'] = $jobApplication->resumeData['error']; // isResumeCompatible() sets this error
					$extractor->saveToSession($_POST);
				}

			}
		}

		/* Saving job application */

		if (is_null($correctResume) || $correctResume) { // Try saving only if resume is compatible or no resume has been uploaded

			try {

			    $jobApplication->save(); // Throws exceptions on failiures
				$extractor->removeFromSession();

				/* Send mail notifications */
				$notifier = new RecruitmentMailNotifier();
				$notifier->sendApplicationReceivedEmailToManager($jobApplication);

				if (!$notifier->sendApplicationReceivedEmailToApplicant($jobApplication)) {
				    $objs['error']['applicantEmailError'] = 'Emailing applicant failed';
				}

				$objs['savingStatus'] = true;

			} catch (JobApplicationException $e) {
				$objs['savingStatus'] = false;
				$extractor->saveToSession($_POST);
			}

		}

		$path = '/templates/recruitment/applicant/jobApplicationStatus.php';
		$objs['application'] = $jobApplication;
		$objs['vacancy'] = JobVacancy::getJobVacancy($jobApplication->getVacancyId());
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

	/* TODO: Move this method to a model */
	private static function _getEmployeeSearchList() {
		$employeeSearchList = array();

		$selecteFields[] = 'CONCAT(em.`emp_firstname`, \' \', em.`emp_lastname`)';
		$selecteFields[] = 'jt.`jobtit_name`';
		$selecteFields[] = 'em.`emp_number`';

		$selectTables[] = '`hs_hr_employee` AS em';
		$selectTables[] = '`hs_hr_job_title` AS jt';

		$joinConditions[1] = 'jt.`jobtit_code` = em.`job_title_code`';

		$orderCondition = $selecteFields[1];
		$selectConditions[] = "(em.`emp_status` != 'EST000' OR em.`emp_status` IS NULL)";
		$sqlBuilder = new SQLQBuilder();
		$query = $sqlBuilder->selectFromMultipleTable($selecteFields, $selectTables, $joinConditions, $selectConditions, null, $orderCondition);

		$query = preg_replace("/\\\'/", "'", $query);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($query);

		$result = $dbConnection->executeQuery($query);

		while($row = mysql_fetch_array($result, MYSQL_NUM)) {
			$row[0] = addslashes($row[0]);
			$employeeSearchList[] = $row;
		}

		return $employeeSearchList;
	}

    /**
     * Display list of job applications to HR admin or manager
     */
    private function _viewApplicationList() {

        if ($this->authorizeObj->isAdmin() || $this->authorizeObj->isManager() || $this->authorizeObj->isDirector() || $this->authorizeObj->isAcceptor() || $this->authorizeObj->isOfferer()) {
            $managerId = $this->authorizeObj->isAdmin()? null : $this->authorizeObj->getEmployeeId();
            $applications = JobApplication::getList($managerId);

            $path = '/templates/recruitment/applicationList.php';
            $objs['applications'] = $applications;
            $template = new TemplateMerger($objs, $path);
            $template->display();
        } else {
            $this->_notAuthorized();
        }
    }

    /**
     * View application details
     * @param int $id Application ID
     */
    private function _viewApplicationDetails($id) {
        $path = '/templates/recruitment/viewApplicationDetails.php';

        $objs['application'] = JobApplication::getJobApplication($id);

        $template = new TemplateMerger($objs, $path);
        $template->display();
    }

    /**
     * View application history
     * @param int $id Application ID
     */
    private function _viewApplicationHistory($id) {
        $path = '/templates/recruitment/viewApplicationHistory.php';
        $objs['application'] = JobApplication::getJobApplication($id);

        $template = new TemplateMerger($objs, $path);
        $template->display();
    }

    /**
     * Deletes the resume of the applicant
     * @param int $applicationId ID of the Application
     */

	private function _deleteResume($applicationId) {

		$this->_authenticateResumeAccess();

		$jobApplication = new JobApplication();
		$jobApplication->setId($applicationId);

		// If resume details are not set, at default they become null

		try {
		    $jobApplication->deleteResume();
		    $objs['application'] = JobApplication::getJobApplication($applicationId); // No resume
		    $objs['message'] = 'Resume deleted';
		} catch (Exception $e) {
		    $objs['application'] = JobApplication::getJobApplication($applicationId); // still contains the resume
		    $objs['message'] = 'Resume not deleted';
		}

        $path = '/templates/recruitment/viewApplicationDetails.php';
        $template = new TemplateMerger($objs, $path);
        $template->display();

	}

    /**
     * Replaces the resume of the applicant.
     * $_POST contains the application ID
     */

	private function _replaceResume() {

		$this->_authenticateResumeAccess();

		$extractor = new EXTRACTOR_JobApplication();
		$jobApplication = $extractor->parseData($_POST);

		if ($jobApplication->resumeData['size'] > 0) {

			if ($jobApplication->isResumeCompatible()) {

				try {
				    $jobApplication->updateResume();
				    $objs['application'] = JobApplication::getJobApplication($jobApplication->getId()); // Contains new resume
				    $objs['message'] = 'Resume replaced';
				} catch (Exception $e) {
				    $objs['application'] = JobApplication::getJobApplication($jobApplication->getId()); // Still contains the old resume
				    $objs['message'] = 'Resume not replaced';
				}

			} else {
			    $objs['application'] = JobApplication::getJobApplication($jobApplication->getId());
			    $objs['message'] = $jobApplication->resumeData['error'];
			}


		} else {
		    $objs['application'] = JobApplication::getJobApplication($jobApplication->getId());
		}

        $path = '/templates/recruitment/viewApplicationDetails.php';
        $template = new TemplateMerger($objs, $path);
        $template->display();

	}

    /**
     * Reject the given application
     * @param JobApplicationEvent Job Application event with the details
     */
    private function _rejectApplication($event) {
        if ($this->authorizeObj->isAdmin() || $this->authorizeObj->isManager() || $this->authorizeObj->isDirector() || $this->authorizeObj->isAcceptor() || $this->authorizeObj->isOfferer()) {

            // TODO: Validate if Hiring manager or interview manager and in correct status
            $application = JobApplication::getJobApplication($event->getApplicationId());
            $application->setStatus(JobApplication::STATUS_REJECTED);
            try {
                $application->save();
                $this->_saveApplicationEvent($event, JobApplicationEvent::EVENT_REJECT);

                // Send notification to Applicant
                $notifier = new RecruitmentMailNotifier();
                $notifier->sendApplicationRejectedEmailToApplicant($application);

                $message = 'UPDATE_SUCCESS';
            } catch (Exception $e) {
                $message = 'UPDATE_FAILURE';
            }
            $this->redirect($message, '?recruitcode=Application&action=List');
            //$this->_viewApplicationList();
        } else {
            $this->_notAuthorized();
        }
    }

    private function _saveFirstInterview($event) {
        if ($this->authorizeObj->isAdmin() || $this->authorizeObj->isManager() || $this->authorizeObj->isOfferer()) {

            // TODO: Validate if Hiring manager or interview manager and in correct status
            $applicationId = $event->getApplicationId();
            $application = JobApplication::getJobApplication($applicationId);
            $application->setStatus(JobApplication::STATUS_FIRST_INTERVIEW_SCHEDULED);

            try {
                $application->save();
                $event->setEventType(JobApplicationEvent::EVENT_SCHEDULE_FIRST_INTERVIEW);
                $event->setStatus(JobApplicationEvent::STATUS_INTERVIEW_SCHEDULED);
                $event->setCreatedBy($_SESSION['user']);
                $event->save();

                // Send notification to Interviewer
                $notifier = new RecruitmentMailNotifier();
                $notifier->sendInterviewTaskToManager($event);

                $message = 'UPDATE_SUCCESS';
            } catch (Exception $e) {
                $message = 'UPDATE_FAILURE';
            }
            $this->redirect($message, '?recruitcode=Application&action=List');
            //$this->_viewApplicationList();
        } else {
            $this->_notAuthorized();
        }
    }

    private function _saveSecondInterview($event) {
        if ($this->authorizeObj->isAdmin() || $this->authorizeObj->isManager() || $this->authorizeObj->isOfferer()) {

            // TODO: Validate if Hiring manager or interview manager and in correct status
            $applicationId = $event->getApplicationId();
            $application = JobApplication::getJobApplication($applicationId);
            $application->setStatus(JobApplication::STATUS_SECOND_INTERVIEW_SCHEDULED);

            try {
                $application->save();
                $event->setEventType(JobApplicationEvent::EVENT_SCHEDULE_SECOND_INTERVIEW);
                $event->setStatus(JobApplicationEvent::STATUS_INTERVIEW_SCHEDULED);
                $event->setCreatedBy($_SESSION['user']);

                $event->save();

                // Send notification to Interviewer
                $notifier = new RecruitmentMailNotifier();
                $notifier->sendInterviewTaskToManager($event);

                $message = 'UPDATE_SUCCESS';
            } catch (Exception $e) {
                $message = 'UPDATE_FAILURE';
            }

            $this->redirect($message, '?recruitcode=Application&action=List');
            //$this->_viewApplicationList();
        } else {
            $this->_notAuthorized();
        }
    }


    /**
     * Confirm the given action by showing a confirmation page to the user
     *
     * @param int $id The Job Application ID
     * @param int $action The action constant
     */
    private function _confirmAction($id, $action) {
        $path = '/templates/recruitment/confirmAction.php';

        $objs['application'] = JobApplication::getJobApplication($id);
        $objs['action'] = $action;

        $template = new TemplateMerger($objs, $path);
        $template->display();
    }

    private function _scheduleFirstInterview($id) {
        if ($this->authorizeObj->isAdmin() || $this->authorizeObj->isManager() || $this->authorizeObj->isOfferer()) {
            $this->_scheduleInterview($id, 1);
        } else {
            $this->_notAuthorized();
        }
    }

    private function _scheduleSecondInterview($id) {
        if ($this->authorizeObj->isAdmin() || $this->authorizeObj->isManager() || $this->authorizeObj->isOfferer()) {
            $this->_scheduleInterview($id, 2);
        } else {
            $this->_notAuthorized();
        }
    }

    private function _scheduleInterview($id, $num) {
        $path = '/templates/recruitment/scheduleInterview.php';

        $empInfo = new EmpInfo();
        $objs['noOfEmployees'] = $empInfo->countEmployee();
        $objs['employeeSearchList'] = $this->_getEmployeeSearchList();
        $objs['application'] = JobApplication::getJobApplication($id);
        $objs['interview'] = $num;

        $template = new TemplateMerger($objs, $path);
        $template->display();
    }

    private function _offerJob($event) {
        if ($this->authorizeObj->isAdmin() || $this->authorizeObj->isManager() || $this->authorizeObj->isOfferer()) {

            // TODO: Validate if Hiring manager or interview manager and in correct status
            $application = JobApplication::getJobApplication($event->getApplicationId());
            $application->setStatus(JobApplication::STATUS_JOB_OFFERED);

            try {
                $application->save();
                $this->_saveApplicationEvent($event, JobApplicationEvent::EVENT_OFFER_JOB);
                $message = 'UPDATE_SUCCESS';
            } catch (Exception $e) {
                $message = 'UPDATE_FAILURE';
            }

            $this->redirect($message, '?recruitcode=Application&action=List');
            //$this->_viewApplicationList();
        } else {
            $this->_notAuthorized();
        }
    }
    private function _markDeclined($event) {
        if ($this->authorizeObj->isAdmin() || $this->authorizeObj->isManager() || $this->authorizeObj->isOfferer()) {

            // TODO: Validate if Hiring manager or interview manager and in correct status
            $application = JobApplication::getJobApplication($event->getApplicationId());
            $application->setStatus(JobApplication::STATUS_OFFER_DECLINED);

            try {
                $application->save();
                $this->_saveApplicationEvent($event, JobApplicationEvent::EVENT_MARK_OFFER_DECLINED);

                $notifier = new RecruitmentMailNotifier();
                $notifier->sendApplicationRejectedEmailToApplicant($application);

                $message = 'UPDATE_SUCCESS';
            } catch (Exception $e) {
                $message = 'UPDATE_FAILURE';
            }

            $this->redirect($message, '?recruitcode=Application&action=List');
            //$this->_viewApplicationList();
        } else {
            $this->_notAuthorized();
        }
    }

    /**
     * Show a screen allowing the manager to select a director
     * to seek approval from. Also allows the manager to add notes
     * related to the hiring.
     *
     * @param int $id Id of job application
     */
    private function _confirmSeekApproval($id) {
        $path = '/templates/recruitment/seekApproval.php';

        $empInfo = new EmpInfo();
        $objs['employeeSearchList'] = $this->_getEmployeeSearchList();
        $objs['application'] = JobApplication::getJobApplication($id);

        $template = new TemplateMerger($objs, $path);
        $template->display();
    }

    private function _seekApproval($event) {
        if ($this->authorizeObj->isAdmin() || $this->authorizeObj->isManager() || $this->authorizeObj->isOfferer()) {

            // TODO: Validate if Hiring manager or interview manager and in correct status
            $application = JobApplication::getJobApplication($event->getApplicationId());
            $application->setStatus(JobApplication::STATUS_PENDING_APPROVAL);

            try {
                $application->save();

                $event->setEventType(JobApplicationEvent::EVENT_SEEK_APPROVAL);
                $event->setCreatedBy($_SESSION['user']);
                $event->save();

                // Send notification to Interviewer
                $notifier = new RecruitmentMailNotifier();
                $mailResult = $notifier->sendSeekApprovalToDirector($application, $event);

                $message = 'UPDATE_SUCCESS';
            } catch (Exception $e) {
                $message = 'UPDATE_FAILURE';
            }

            $this->redirect($message, '?recruitcode=Application&action=List');

        } else {
            $this->_notAuthorized();
        }
    }
    private function _approve($event) {
        if ($this->authorizeObj->isAdmin() || $this->authorizeObj->isDirector() || $this->authorizeObj->isAcceptor()) {

            // TODO: Validate if Hiring manager or interview manager and in correct status
            $application = JobApplication::getJobApplication($event->getApplicationId());
            $application->setStatus(JobApplication::STATUS_HIRED);

            try {
                $application->save();
                $this->_saveApplicationEvent($event, JobApplicationEvent::EVENT_APPROVE);

                // Create employee in PIM
                $empId = $this->createEmployeeFromApplication($application);

                // Save new employee number in application for reference.
                $application->setEmpNumber($empId);
                $application->save();

                // Send email informing approval to hiring manager
                $notifier = new RecruitmentMailNotifier();
                $mailResult = $notifier->sendApprovalToHiringManager($application, $event);

                $message = 'UPDATE_SUCCESS';
            } catch (Exception $e) {
                $message = 'UPDATE_FAILURE';
            }

            $this->redirect($message, '?recruitcode=Application&action=List');
            //$this->_viewApplicationList();
        } else {
            $this->_notAuthorized();
        }

    }

    /**
     * Add given event to application
     * @param JobApplicationEvent Job Application event with the details
     * @param int Event type
     */
    private function _saveApplicationEvent($event, $eventType) {

        $event->setEventType($eventType);
        $createdTime = date(LocaleUtil::STANDARD_DATETIME_FORMAT);
        $event->setCreatedTime($createdTime);
        $event->setCreatedBy($_SESSION['user']);
        //$event->setStatus($status);

        $event->save();
    }

    /**
     * Save the new values of passed Job Application Event
     */
    private function _editEvent($jobApplicationEvent) {
        try {
            $jobApplicationEvent->save();
            $message = 'UPDATE_SUCCESS';
        } catch (JobApplicationEventException $e) {
            $message = 'UPDATE_FAILURE';
        }
        $this->redirect($message);
    }

    /**
     * Create an employee based on a job application.
     *
     * @param JobApplication $jobApplication Job Application to create the employee from.
     * @throws RecruitmentControllerException if there is an error when creating employee
     */
    public function createEmployeeFromApplication($jobApplication) {

        $empInfo = new EmpInfo();

        // main information
        $employeeId = $empInfo->getLastId();
        $empInfo->setEmployeeId($employeeId);
        $empInfo->setEmpLastName($jobApplication->getLastName());
        $empInfo->setEmpFirstName($jobApplication->getFirstName());
        $empInfo->setEmpMiddleName($jobApplication->getMiddleName());
        $result = $empInfo->addEmpMain();

        // contact information
        $empInfo->setEmpStreet1($jobApplication->getStreet1());
        $empInfo->setEmpStreet2($jobApplication->getStreet2());
        $empInfo->setEmpCity($jobApplication->getCity());
        $empInfo->setEmpProvince($jobApplication->getProvince());
        $empInfo->setEmpCountry($jobApplication->getCountry());
        $empInfo->setEmpZipCode($jobApplication->getZip());
        $empInfo->setEmpHomeTelephone($jobApplication->getPhone());
        $empInfo->setEmpMobile($jobApplication->getMobile());
        $empInfo->setEmpOtherEmail($jobApplication->getEmail());
        $result = $empInfo->updateEmpContact();

        // job information
        $vacancy = JobVacancy::getJobVacancy($jobApplication->getVacancyId());
        $empInfo->setEmpJobTitle($vacancy->getJobTitleCode());
        $empInfo->setEmpStatus(0);
        $empInfo->setEmpEEOCat(0);
        $empInfo->setEmpJoinedDate("null");
        $empInfo->setEmpTerminatedDate("null");
        $result = $empInfo->updateEmpJobInfo();

        return $empInfo->getEmpId();
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

	/**
	 * Only allowed users should be able to proceed with resume related functions
	 */
	private function _authenticateResumeAccess() {

		if (!$this->authorizeObj->isAdmin() &&
			!$this->authorizeObj->isManager() &&
			!$this->authorizeObj->isDirector() &&
			!$this->authorizeObj->isAcceptor() &&
			!$this->authorizeObj->isOfferer()) {

			$this->_notAuthorized();

		}

	}

    /**
     * Show not authorized message
     */
    private function _notAuthorized() {
        trigger_error("Not Authorized!", E_USER_NOTICE);
    }
}

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
 * Actions class for performance module
 */
class performanceActions extends sfActions {
	
	private $kpiService ;
	private $jobService ;
	private $performanceKpiService;
	private $performanceReviewService;
	
	
	/**
	 * Get Job Service 
	 */
	public function getJobService(){
		$this->jobService	=	new JobService();
		return $this->jobService;
	}
	
	/**
	 * Set Job Service
	 * @param JobService $jobService
	 * @return unknown_type
	 */
	public function setJobService(JobService $jobService){
		$this->jobService	=	$jobService ;
	}
	
	/**
	 * Get Kpi Service
	 * @return KpiService
	 */
	public function getKpiService( ){
		$this->kpiService	=	new KpiService();
		$this->kpiService->setKpiDao(new KpiDao());
		return $this->kpiService ;
	}
	
	/**
	 * Set Kpi Service
	 * 
	 * @param KpiService $kpiService
	 * @return void
	 */
	public function setKpiService( KpiService $kpiService){
		$this->kpiService = $kpiService;
	}
	
	/**
	 * Get Job Service 
	 */
	public function getPerformanceKpiService(){
		$this->performanceKpiService	=	new PerformanceKpiService();
		return $this->performanceKpiService;
	}
	
	/**
	 * Set Job Service
	 * @param JobService $jobService
	 * @return unknown_type
	 */
	public function setPerformanceKpiService(PerformanceKpiService $performanceKpiService){
		$this->performanceKpiService	=	$performanceKpiService ;
	}
	
	/**
	 * Get Job Service 
	 */
	public function getPerformanceReviewService(){
		$this->performanceReviewService	=	new PerformanceReviewService();
		$this->performanceReviewService->setPerformanceReviewDao(new PerformanceReviewDao());
		return $this->performanceReviewService;
	}
	
	/**
	 * Set Job Service
	 * @param JobService $jobService
	 * @return unknown_type
	 */
	public function setPerformanceReviewService(PerformanceReviewService $performanceReviewService){
		$this->performanceReviewService	=	$PerformanceReviewService ;
	}
	
	/**
	 * This method is executed before each action
	 */
	public function preExecute() {
		
		if (!empty($_SESSION['empNumber'])) {
		    $this->loggedEmpId = $_SESSION['empNumber'];
		} else {
		    $this->loggedEmpId = 0; // Means default admin
		}
		
		if(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']=='Yes') {
		    $this->loggedAdmin = true;
		} else {
		    $this->loggedAdmin = false;
		}
		
		$this->loggedReviewer = $this->isLoggedReviewer($_SESSION['empNumber']);

        if (isset($_SESSION['user'])) {
            $this->loggedUserId = $_SESSION['user'];
        }
	    
	}
	
	/**
	 * List Define Kpi
	 * @param sfWebRequest $request
	 * @return unknown_type
	 */
	public function executeListDefineKpi(sfWebRequest $request) {

        $this->form = new ListKpiForm(array(), array(), true);
        
		$jobService 				=	$this->getJobService();
		$this->listJobTitle		=	$jobService->getJobTitleList('job.id','ASC', array(0,1));
		
		$kpiService 			= 	$this->getKpiService();
		$this->mode				=	$request->getParameter('mode');
		
		$this->pager = new SimplePager('KpiList', sfConfig::get('app_items_per_page'));
		$this->pager->setPage(($request->getParameter('page')!='')?$request->getParameter('page'):0);
		 
		if($request->getParameter('mode')=='search')
        {
			$jobTitleId				=	$request->getParameter('txtJobTitle') ;
			if($jobTitleId != 'all'){
				$this->searchJobTitle	=	$jobService->readJobTitle( $jobTitleId );
				
				$this->kpiList 			= 	$kpiService->getKpiForJobTitle( $jobTitleId);
			}else{
				
				$this->pager->setNumResults($kpiService->getCountKpiList());
		       	$this->pager->init();
		        $offset = $this->pager->getOffset();
		        $offset = empty($offset)?0:$offset;
		        $limit 	= $this->pager->getMaxPerPage();
	        	
	        	$this->kpiList = $kpiService->getKpiList($offset,$limit);
				$this->kpiList = $kpiService ->getKpiList();
			}
        	
        }else
        {
	        
	       
	        $this->pager->setNumResults($kpiService->getCountKpiList());
	       	$this->pager->init();
	       	
	        $offset = $this->pager->getOffset();
	        $offset = empty($offset)?0:$offset;
	        $limit 	= $this->pager->getMaxPerPage();
        	
        	$this->kpiList = $kpiService->getKpiList($offset,$limit);
        }
        
       $this->hasKpi	=	( count($this->kpiList)>0 )?true:false;
		
	}

	
	
	
	/**
	 * Save Kpi
	 * @param sfWebRequest $request
	 * @return None
	 */
	public function executeSaveKpi(sfWebRequest $request) {

            $this->form = new SaveKpiForm(array(), array(), true);
		
            $jobService	= $this->getJobService();
            $this->listJobTitle = $jobService->getJobTitleList();

            $kpiService = $this->getKpiService();
            $this->defaultRate = $kpiService->getKpiDefaultRate();

            if ($request->isMethod('post')) {

                $this->form->bind($request->getParameter($this->form->getName()));

                if ($this->form->isValid()) {
                
                    try {

                        $defineKpi = new DefineKpi();
                        $defineKpi->setJobtitlecode($request->getParameter('txtJobTitle'));
                        $defineKpi->setDesc(trim($request->getParameter('txtDescription')));

                        if (trim($request->getParameter('txtMinRate')) != "") {
                            $defineKpi->setMin($request->getParameter('txtMinRate'));
                        }

                        if (trim($request->getParameter('txtMaxRate')) != ""){
                            $defineKpi->setMax($request->getParameter('txtMaxRate'));
                        }

                        if ($request->getParameter('chkDefaultScale') == 1) {
                            $defineKpi->setDefault(1);
                        } else {
                            $defineKpi->setDefault(0);
                        }

                        $defineKpi->setIsactive(1);

                        $kpiService->saveKpi($defineKpi);

                        $this->setMessage('SUCCESS',array('Successfully Added <a href="listDefineKpi"> View KPI List</a>'));
                        $this->redirect('performance/saveKpi');

                    } catch (Doctrine_Validator_Exception $e) {

                        $this->setMessage('WARNING',array($e->getMessage()));
                        $this->errorMessage	= $e->getMessage();

                    }

                }

            }

	}
	
	/**
	 * Update Define Kpis
	 * @param sfWebRequest $request
	 * @return unknown_type
	 */
	public function executeUpdateKpi(sfWebRequest $request) {

            $jobService = $this->getJobService();
            $this->listJobTitle = $jobService->getJobTitleList('job.id','ASC',array(JobTitle::JOB_STATUS_ACTIVE,JobTitle::JOB_STATUS_DELETED));

            $kpiService = $this->getKpiService();
            $this->defaultRate = $kpiService->getKpiDefaultRate();

            $kpi = $kpiService->readKpi($request->getParameter('id'));
            $this->kpi = $kpi;

            if ($request->isMethod('post')) {

                $kpi->setJobtitlecode($request->getParameter('txtJobTitle'));
                $kpi->setDesc (trim($request->getParameter('txtDescription')));

                if (trim($request->getParameter('txtMinRate')) != "") {
                    $kpi->setMin($request->getParameter('txtMinRate'));
                } else {
                    $kpi->setMin(null);
                }

                if(trim($request->getParameter('txtMaxRate')) != "") {
                    $kpi->setMax($request->getParameter('txtMaxRate'));
                } else {
                    $kpi->setMax(null);
                }

                if ($request->getParameter('chkDefaultScale') == 1) {
                    $kpi->setDefault(1);
                } else {
                    $kpi->setDefault(0);
                }

                $kpiService->saveKpi($kpi);
                $this->setMessage('SUCCESS',array('Successfully Updated'));
                $this->redirect ( 'performance/listDefineKpi' );

            }
		
	}
	
	/**
	 * Copy define Kpi's into new Job Title
	 *
	 * @param sfWebRequest $request
	 * $return none
	 **/
	public function executeCopyKpi(sfWebRequest $request) {

            $this->form = new CopyKpiForm(array(), array(), true);

            $kpiService = $this->getKpiService();

            $jobService	= $this->getJobService();
            $this->listJobTitle	= $jobService->getJobTitleList();
            $this->listAllJobTitle = $jobService->getJobTitleList('job.id', 'ASC', array(JobTitle::JOB_STATUS_ACTIVE,JobTitle::JOB_STATUS_DELETED));

            if ($request->isMethod('post')) {

                $this->form->bind($request->getParameter($this->form->getName()));

                if ($this->form->isValid()) {

                    $toJobTitle = $request->getParameter('txtCopyJobTitle');
                    $fromJobTitle =	$request->getParameter('txtJobTitle');
                    $confirm = $request->getParameter('txtConfirm');

                    $avaliableKpiList = $kpiService->getKpiForJobTitle($toJobTitle);
                    $this->toJobTitle = $toJobTitle;
                    $this->fromJobTitle = $fromJobTitle;

                    if( count($avaliableKpiList) == 0 || $confirm == '1'){

                        $kpiService->copyKpi( $toJobTitle,$fromJobTitle);

                        $this->setMessage('SUCCESS',array('Successfully Copied'));
                        $this->redirect('performance/listDefineKpi');

                    }else{

                        $this->confirm = true;

                    }
                
                }

            }
		
	}
	
	/**
	 * Delete Define Kpi
	 * @param sfWebRequest $request
	 * @return none
	 */
	public function executeDeleteDefineKpi(sfWebRequest $request) {

            $this->form = new ListKpiForm(array(), array(), true);

            if ($request->isMethod('post')) {

                $this->form->bind($request->getParameter($this->form->getName()));

                if ($this->form->isValid()) {

                    $kpiService = $this->getKpiService();
                    $kpiService->deleteKpi($request->getParameter('chkKpiID'));

                    $this->setMessage('SUCCESS',array('Successfully Deleted'));

                }

            }
            
            $this->redirect('performance/listDefineKpi');
		
	}
	
	/**
	 * View Performance review
	 * @param sfWebRequest $request
	 * @return none
	 */
	public function executePerformanceReview(sfWebRequest $request) {

            $this->form = new PerformanceReviewForm(array(), array(), true);
		
            $id = $request->getParameter('id');

            $performanceReviewService =	$this->getPerformanceReviewService();
            $performanceReview = $performanceReviewService->readPerformanceReview($id);
            $performanceService	= $this->getPerformanceKpiService();

            $this->performanceReview =	$performanceReview ;
            $performanceKpiList = $performanceService->getPerformanceKpiList($performanceReview->getKpis());
            $this->kpiList = $performanceKpiList ;

            $this->isHrAdmin = $this->isHrAdmin();
            $this->isReviwer = $this->isReviwer($performanceReview);

            if ($request->isMethod('post')) {

                $this->form->bind($request->getParameter($this->form->getName()));

                if ($this->form->isValid()) {

                    $saveMode = $request->getParameter('saveMode');
                    $rates = $request->getParameter('txtRate');
                    $comments = $request->getParameter('txtComments');

                    $performanceReview->setLatestComment($request->getParameter('txtMainComment'));

                    if(count($rates)) {

                        $performanceKpiService = $this->getPerformanceKpiService();
                        $modifyperformanceKpiList =	array();

                        foreach($performanceKpiList as $performanceKpi) {

                            $performanceKpi->setRate($rates[$performanceKpi->getId()]);
                            $performanceKpi->setComment($comments[$performanceKpi->getId()]);
                            array_push($modifyperformanceKpiList,$performanceKpi);

                        }

                        $performanceReview->setKpis($performanceKpiService->getXml($modifyperformanceKpiList));
                        $performanceReviewService->savePerformanceReview($performanceReview);

                    }

                    switch( $saveMode ) {

                        case 'save': if ($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED) {
                                        $performanceReviewService->changePerformanceStatus($performanceReview,PerformanceReview::PERFORMANCE_REVIEW_STATUS_BEING_REVIWED);
                                     }
                                     break;

                        case 'submit': $performanceReviewService->changePerformanceStatus($performanceReview ,PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED);
                                       break;

                        case 'reject': $performanceReviewService->changePerformanceStatus($performanceReview ,PerformanceReview::PERFORMANCE_REVIEW_STATUS_REJECTED);
                                       break;

                        case 'approve': $performanceReviewService->changePerformanceStatus($performanceReview ,PerformanceReview::PERFORMANCE_REVIEW_STATUS_APPROVED);
                                        break;

                    }

                    if (trim($request->getParameter('txtMainComment')) != '') {
                        $performanceReviewService->addComment( $performanceReview ,$request->getParameter ( 'txtMainComment' ),$_SESSION['empNumber']);
                    }

                    $this->redirect ( 'performance/performanceReview?id='.$id );

                }

            }
			
	}
	
	/**
	 * Get the current page number from the user session.
	 * @return int Page number
	 */
	protected function getPage() {
		return $this->getUser()->getAttribute ( 'performancereviewlist.page', 1, 'performancereview_module' );
	}
	
	/**
	 * Set's the current page number in the user session.
	 * @param $page int Page Number
	 * @return None
	 */
	protected function setPage($page) {
		$this->getUser()->setAttribute ( 'performancereviewlist.page', $page, 'performancereview_module' );
	}
	
	/**
	 * Is HR admin
	 * @return unknown_type
	 */
	protected function isHrAdmin( )
	{
		if(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']=='Yes')
			return true ;
		else
			return false;
	}
	

	/**
	 * Is HR admin
	 * @return unknown_type
	 */
	protected function isReviwer( PerformanceReview $performanceReview )
	{
		if($performanceReview->getReviewerId() == $_SESSION['empNumber'] )
			return true ;
		else
			return false;
	}
	
	/**
	 * Checks whether the logged in employee is a reviewer
	 */
	
	protected function isLoggedReviewer($empId) {
		
		$performanceReviewService = $this->getPerformanceReviewService();
	    
	    return $performanceReviewService->isReviewer($empId);
	    
	}
	
 	/**
     * Set message 
     */
    public function setMessage( $messageType , $message = array())
    {
    	$this->getUser()->setFlash('messageType', $messageType);
    	$this->getUser()->setFlash('message', $message);
    }
    
    /**
     * Save performance Review
     * @param $request
     * @return unknown_type
     */
	public function executeSaveReview(sfWebRequest $request) {

            $this->form = new SaveReviewForm(array(), array(), true);

            /* Showing Performance Review Add form*/

            $companyService	= new CompanyService();
            $this->empJson = $companyService->getEmployeeListAsJson();

            /* Saving Performance Reviews */

            if ($request->isMethod('post')) {

                $this->form->bind($request->getParameter($this->form->getName()));

                if ($this->form->isValid()) {

                    /* Showing update form: Begins */

                    if ($request->getParameter('editReview') && count($request->getParameter('chkReview')) == 0) {
                        $this->getUser()->setFlash('templateMessage', array('WARNING', 'Please select a review to edit.'));
                        $this->redirect('performance/viewReview');
                    }

                    if ($request->getParameter('chkReview')) {

                        $reviewIds = $request->getParameter('chkReview');

                        if (count($reviewIds) > 1) {
                            $this->getUser()->setFlash('templateMessage', array('WARNING', 'Please select only one review at a time for editing.'));
                            $this->redirect('performance/viewReview');
                        }

                        $performanceReviewService = $this->getPerformanceReviewService();
                        $review = $performanceReviewService->readPerformanceReview($reviewIds[0]);
                        $this->clues = $this->getReviewSearchClues($review);

                        return;

                    }

                    /* Showing update form: Ends */

                    $clues = $this->getReviewSearchClues($request, '-0');
                    $this->clues = $clues;

                    if ($request->getParameter("hdnId-0")) { // Review ID
                        $this->clues['id'] = $request->getParameter("hdnId-0");
                    }

                    $employeeService = new EmployeeService();
                    $employee = $employeeService->getEmployee($clues['empId']);
                    $empJobCode = $employee->getJobTitleCode();
                    $subDivisionId = $employee->getWorkStation();

                    /* Checking whether wrong employee */
                    if (!$this->_isCorrectEmployee($this->clues['empId'], $this->clues['empName'])) {
                        $this->templateMessage = array('WARNING', 'No employee exists with this name.');
                        return;
                    }

                    /* Checking whether wrong reviewer */
                    if (!$this->_isCorrectEmployee($this->clues['reviewerId'], $this->clues['reviewerName'])) {
                        $this->templateMessage = array('WARNING', 'No reviewer exists with this name.');
                        return;
                    }

                    if (empty($empJobCode)) {

                       if(trim($request->getParameter("hdnId-0")) == "") {

                          $this->templateMessage = array('WARNING', 'Employee is not assigned a job title, cannot add a performance review');
                          return;

                       }

                       $empJobCode = $this->getPerformanceReviewService()->readPerformanceReview($request->getParameter("hdnId-0"))->getJobTitleCode();

                    }

                    $kpiService = $this->getKpiService();
                    $performanceKpiService = $this->getPerformanceKpiService();
                    $kpiList = $kpiService->getKpiForJobTitle($empJobCode);

                    if (count($kpiList) == 0) {

                        $this->templateMessage = array('WARNING', 'No Key Performance Indicators were found for the job title of this employee. <a href="saveKpi">Define now</a>');
                        return;

                    }

                    $performanceReviewService = $this->getPerformanceReviewService();

                    if ($request->getParameter("hdnId-0")) { // Updating an existing one
                        $review = $performanceReviewService->readPerformanceReview($request->getParameter("hdnId-0"));
                    } else { // Adding a new one
                        $review = new PerformanceReview();
                    }

                    $xmlStr = $performanceKpiService->getXmlFromKpi($kpiService->getKpiForJobTitle($empJobCode));

                    $review->setEmployeeId($clues['empId']);
                    $review->setReviewerId($request->getParameter("hdnReviewerId-0"));
                    $review->setCreatorId($this->loggedUserId);
                    $review->setJobTitleCode($empJobCode);
                    $review->setSubDivisionId($subDivisionId);
                    $review->setCreationDate(date('Y-m-d'));
                    $review->setPeriodFrom( date("Y-m-d",strtotime($request->getParameter("txtPeriodFromDate-0"))));
                    $review->setPeriodTo(date("Y-m-d",strtotime($request->getParameter("txtPeriodToDate-0"))) );
                    $review->setDueDate(date("Y-m-d",strtotime($request->getParameter("txtDueDate-0"))) );
                    $review->setState(PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED);
                    $review->setKpis($xmlStr);

                    $performanceReviewService->savePerformanceReview($review);
                    $performanceReviewService->informReviewer($review);

                    $this->getUser()->setFlash('prClues', $clues);

                    $actionResult = ($request->getParameter("hdnId-0"))?'updated':'added';
                    $this->templateMessage = array('SUCCESS', 'Successfully '.$actionResult.' review of '.$this->clues['empName'].'. <a href="viewReview">View</a>');
		    
                }

            }
	    	    
	}
	
	/**
	 * Handles showing review search form and
	 * listing searched reviews.
	 */
	
	public function executeViewReview(sfWebRequest $request) {
		
            /* Showing Performance Review Search form
             * ====================================== */

            $this->form = new ViewReviewForm(array(), array(), true);

            $performanceReviewService = $this->getPerformanceReviewService();

            /* Job title list */
            $jobService	= $this->getJobService();
            $this->jobList = $jobService->getJobTitleList('job.id', 'ASC',
                                             array(JobTitle::JOB_STATUS_DELETED, JobTitle::JOB_STATUS_ACTIVE));

            /* Employee list */
            if ($this->loggedAdmin) {
                    $employeeService = new EmployeeService();
                    $this->empJson = $employeeService->getEmployeeListAsJson();
            } elseif ($this->loggedReviewer) {
                $this->empJson = $performanceReviewService->getRevieweeListAsJson($this->loggedEmpId, true);
            }

            /* Subdivision list */
            $companyService = new CompanyService();
            $this->subDivisionList = $companyService->getSubDivisionList();

            /* Checking whether a newly invoked search form */
            $newSearch = false;
            if ($request->getParameter('mode') == 'new') {
                    $newSearch = true;
            }

            /* Preserving search clues */
            $hdnEmpId = $request->getParameter("hdnEmpId");
            if (isset($hdnEmpId) && !$newSearch) { // If the user has performed a new search
                $this->clues = $this->getReviewSearchClues($request);
            } else {

                if ($this->getUser()->hasAttribute('prSearchClues') && !$newSearch) {
                    $this->clues = $this->getUser()->getAttribute('prSearchClues');
                }

                if ($this->getUser()->hasFlash('prClues') && !$newSearch) {
                    $this->clues = $this->getUser()->getFlash('prClues');
                }

            }

            /* Processing reviews
             * ================== */

            if ($request->isMethod('post')) {
                    $page = 1;
                    $this->clues['pageNo'] = 1;
            } elseif ($request->getParameter('page')) {
                    $page = $request->getParameter('page');
                    $this->clues['pageNo'] = $page;
            } elseif ($this->clues['pageNo']) {
                $page = $this->clues['pageNo'];
            }

            /* Preserving search clues */
            if (!isset($this->clues)) {
                    $this->clues = $this->getReviewSearchClues($request);
            }
            $this->getUser()->setAttribute('prSearchClues', $this->clues);

            /* Checking whether wrong seacrch criteria */
            if ((!$this->_isCorrectEmployee($this->clues['empId'], $this->clues['empName'])) ||
                (!$this->_isCorrectEmployee($this->clues['reviewerId'], $this->clues['reviewerName']))
            ) {
                $this->templateMessage = array('WARNING', 'No reviews were found on given criteria');
                return;
            }

            /* Setting logged in user type */
            if (!$this->loggedAdmin && $this->loggedReviewer) {
                $this->clues['loggedReviewerId'] = $this->loggedEmpId;
            } elseif (!$this->loggedAdmin && !$this->loggedReviewer) {
                $this->clues['loggedEmpId'] = $this->loggedEmpId;
            }

            /* Pagination */
            if (!isset($page)) {
                $page = 1;
            }

            $this->pager = new SimplePager('PerformanceReview', sfConfig::get('app_items_per_page'));
            $this->pager->setPage($page);
            $this->pager->setNumResults($performanceReviewService->countReviews($this->clues));
            $this->pager->init();

            /* Fetching reviews */
            $offset = $this->pager->getOffset();
            $offset = empty($offset)?0:$offset;
            $limit = $this->pager->getMaxPerPage();
            $this->reviews = $performanceReviewService->searchPerformanceReview($this->clues, $offset, $limit);

            /* Setting template message */
            if ($this->getUser()->hasFlash('templateMessage')) {
                $this->templateMessage = $this->getUser()->getFlash('templateMessage');
            } elseif (count($this->reviews) == 0) {
                $this->templateMessage = array('WARNING', 'No reviews were found on given criteria');
            }
	    
	}
	
  	/**
     * Show not authorized message
     * 
     */
    public function executeUnauthorized(sfWebRequest $request) {
        sfConfig::set('sf_web_debug', false);
        sfConfig::set('sf_debug', false);

        $response = $this->getResponse();
        $response->setStatusCode(401, 'Not authorized');        
        return $this->renderText("You do not have the proper credentials to access this page!");
    }
    
    public function executeDeleteReview(sfWebRequest $request) {

        $this->form = new ViewReviewForm(array(), array(), true);

        $delReviews = $request->getParameter('chkReview');
        $clues = $this->getReviewSearchClues($request);
        $this->getUser()->setFlash('prClues', $clues);

        if (empty($delReviews)) {
            $this->getUser()->setFlash('templateMessage', array('WARNING', 'Please select reviews to delete.'));
            $this->redirect('performance/viewReview');
        }

        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {

                $performanceReviewService = $this->getPerformanceReviewService();
                $performanceReviewService->deletePerformanceReview($request->getParameter('chkReview'));
                $this->getUser()->setFlash('templateMessage', array('SUCCESS', 'Successfully deleted'));

            }

        }

        $this->redirect('performance/viewReview');

    }

    protected function getReviewSearchClues($request, $suffix='') {
        
            $clues = array();

            if ( $request instanceof  sfWebRequest) {

                $clues['from'] = $request->getParameter('txtPeriodFromDate'.$suffix);
                $clues['to'] = $request->getParameter('txtPeriodToDate'.$suffix);
                $clues['due'] = $request->getParameter('txtDueDate'.$suffix);
                $clues['jobCode'] = $request->getParameter('txtJobTitleCode'.$suffix);
                $clues['divisionId'] = $request->getParameter('txtSubDivisionId'.$suffix);
                $clues['empName'] = $request->getParameter('txtEmpName'.$suffix);
                $clues['empId'] = empty($clues['empName'])?0:$request->getParameter('hdnEmpId'.$suffix);
                $clues['reviewerName'] = $request->getParameter('txtReviewerName'.$suffix);
                $clues['reviewerId'] = empty($clues['reviewerName'])?0:$request->getParameter('hdnReviewerId'.$suffix);
                $clues['pageNo'] = $request->getParameter('hdnPageNo'.$suffix);

            } elseif ( $request instanceof  PerformanceReview ) {

      
                $clues['from'] = $request->getPeriodFrom();
                $clues['to'] = $request->getPeriodTo();
                $clues['due'] = $request->getDueDate();
                $clues['jobCode'] = $request->getJobTitleCode();
                $clues['divisionId'] = $request->getSubDivisionId();
                $clues['empName'] = $request->getEmployee()->getFirstName() . " " . $request->getEmployee()->getLastName();
                $clues['empId'] = $request->getEmployeeId();
                $clues['reviewerName'] = $request->getReviewer()->getFirstName() . " " . $request->getReviewer()->getLastName();
                $clues['reviewerId'] = $request->getReviewerId();
                $clues['id'] = $request->getId();

            }

			return $clues;
        
    }

    protected function _isCorrectEmployee($id, $name) {

        $flag = true;

        if ((!empty($name) && $id == 0)) {
            $flag = false;
        }

        if (!empty($name) && !empty($id)) {

            $employeeService = new EmployeeService();
            $employee = $employeeService->getEmployee($id);

            $nameArray = explode(' ', $name);

            if (count($nameArray) == 2 &&
                strtolower($employee->getFirstName().' '.$employee->getLastName()) != strtolower($name)) {
                $flag = false;
            } elseif (count($nameArray) == 3 &&
                      strtolower($employee->getFullName()) != strtolower($name)) {
                $flag = false;
            }

        }

        if ($flag) {
            return true;
        } else {
            return false;
        }
        
    }
 
    
}

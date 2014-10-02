<?php

class exportToCSVAction extends sfAction {
	public function execute($request) {
		$usrObj = $this->getUser()->getAttribute('user');
        $isAdmin = $usrObj->isAdmin();
		if(!$isAdmin){
			return sfView::NONE;
		}
		
		$this->trackId = $request->getParameter('trackId');
		$service = new PerformanceTrackerService();
		$logs = $service->getPerformanceTrackerLogListByTrack($this->trackId);
		
		$csvContent = $service->getCsvContentDetail($logs);
		$this->renderResponse($csvContent);
		return sfView::NONE;
		
	}	
    
    protected function getFileName(){
    	return "logRecords" . $this->trackId . ".csv";
    }
    
	public function renderResponse($csvContent) {
        ob_clean();
        header("Content-Type: text/csv; charset=UTF-8");
        header("Pragma:''");
        header("Content-Disposition: attachment; filename=" . $this->getFileName());
        
        echo $csvContent;
    }
}
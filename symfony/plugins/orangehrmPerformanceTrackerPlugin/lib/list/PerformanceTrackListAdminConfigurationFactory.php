<?php

class PerformanceTrackListAdminConfigurationFactory extends ohrmListConfigurationFactory {
	
	protected function init() {

		$header1 = new ListHeader();
		$header2 = new ListHeader();
                $header3 = new ListHeader();                
                $header4 = new ListHeader();        

		$header1->populateFromArray(array(
		    'name' => 'Employee',
		    'elementType' => 'link',
		    'elementProperty' => array(
			'labelGetter' => array('getEmployee','getFirstAndLastNames'),
                        'placeholderGetters' => array('id' => 'getId'),
                        'urlPattern' => 'index.php/performanceTracker/addPerformanceTracker/id/{id}'), 
		));  
                
		$header4->populateFromArray(array(
		    'name' => 'Tracker',
		    'elementType' => 'link',
		    'elementProperty' => array(
			'labelGetter' => array('getter' => 'getTrackerName'),
                        'placeholderGetters' => array('id' => 'getId'),
                        'urlPattern' => 'index.php/performanceTracker/addPerformanceTracker/id/{id}'), 
		));  
                
               $header2->populateFromArray(array(
		    'name' => 'Added Date',
                   'width' => '15%',
		    'elementType' => 'labelDate',
		    'elementProperty' => array('getter' => 'getAddedDate'),
		));
               
               $header3->populateFromArray(array(
		    'name' => 'Modified Date',
                   'width' => '15%',
		    'elementType' => 'labelDate',
		    'elementProperty' => array('getter' => 'getModifiedDate'),
		));
                               
		$this->headers = array($header1, $header4, $header2, $header3);
	}

	public function getClassName() {
		return 'PerformanceTrackAdmin';
	}

}

?>

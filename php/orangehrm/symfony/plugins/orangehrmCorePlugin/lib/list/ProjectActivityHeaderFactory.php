<?php

class ProjectActivityHeaderFactory extends ohrmListConfigurationFactory {
	
	protected function init() {

		$header1 = new ListHeader();

		$header1->populateFromArray(array(
		    'name' => 'Activity Name',
		    'elementType' => 'link',
		     'elementProperty' => array(
			'labelGetter' => 'getActivityId',
			'urlPattern' => 'javascript:'),
		));

		$this->headers = array($header1);
	}

	public function getClassName() {
		return 'ProjectActivity';
	}
}

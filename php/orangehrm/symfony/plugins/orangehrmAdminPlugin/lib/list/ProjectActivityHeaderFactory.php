<?php

class ProjectActivityHeaderFactory extends ohrmListConfigurationFactory {
	
	protected function init() {

		$header1 = new ListHeader();

		$header1->populateFromArray(array(
		    'name' => 'Activity Name',
		    'width' => '95%',
		    'elementType' => 'link',
		     'elementProperty' => array(
			'labelGetter' => 'getName',
			'urlPattern' => 'javascript:'),
		));

		$this->headers = array($header1);
	}

	public function getClassName() {
		return 'ProjectActivity';
	}
}

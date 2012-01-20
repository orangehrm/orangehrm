<?php

class EmploymentStatusHeaderFactory extends ohrmListConfigurationFactory {

	protected function init() {

		$header1 = new ListHeader();

		$header1->populateFromArray(array(
		    'name' => 'Employment Status',
		    'elementType' => 'link',
		    'elementProperty' => array(
			'labelGetter' => 'getName',
			'urlPattern' => 'javascript:'),
		));
		
		$this->headers = array($header1);
	}

	public function getClassName() {
		return 'EmploymentStatus';
	}
}

?>

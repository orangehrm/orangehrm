<?php

class WorkShiftHeaderFactory extends ohrmListConfigurationFactory {
	
	protected function init() {

		$header1 = new ListHeader();
		$header2 = new ListHeader();

		$header1->populateFromArray(array(
		    'name' => 'Shift Name',
		    'elementType' => 'link',
		    'elementProperty' => array(
			'labelGetter' => 'getName',
			'urlPattern' => 'javascript:'),
		));
		
		$header2->populateFromArray(array(
		    'name' => 'Hours Per Day',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getHoursPerDay'),
		));

		$this->headers = array($header1, $header2);
	}

	public function getClassName() {
		return 'WorkShift';
	}
}

?>

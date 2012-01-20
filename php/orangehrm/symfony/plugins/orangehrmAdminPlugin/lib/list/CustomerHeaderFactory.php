<?php

class CustomerHeaderFactory extends ohrmListConfigurationFactory {
	
		protected function init() {

		$header1 = new ListHeader();
		$header2 = new ListHeader();

		$header1->populateFromArray(array(
		    'name' => 'Customer',
		    'width' => '49%',
		    'isSortable' => true,
		    'sortField' => 'name',
		    'elementType' => 'link',
		    'elementProperty' => array(
			'labelGetter' => 'getName',
			'placeholderGetters' => array('id' => 'getCustomerId'),
			'urlPattern' => 'addCustomer?customerId={id}'),
		));

		$header2->populateFromArray(array(
		    'name' => 'Description',
		    'width' => '49%',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getDescription'),
		));

		$this->headers = array($header1, $header2);
	}

	public function getClassName() {
		return 'Customer';
	}
}

?>

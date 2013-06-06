<?php

class PayGradeHeaderFactory extends ohrmListConfigurationFactory {

	protected function init() {

		$header1 = new ListHeader();
		$header2 = new ListHeader();
		
		$header1->populateFromArray(array(
		    'name' => 'Pay Grade',
		    'width' => '49%',
		    'isSortable' => true,
		    'sortField' => 'name',
		    'elementType' => 'link',
		    'elementProperty' => array(
			'labelGetter' => 'getName',
			'placeholderGetters' => array('id' => 'getId'),
			'urlPattern' => 'payGrade?payGradeId={id}'),
		));

		$header2->populateFromArray(array(
		    'name' => 'Currency',
		    'width' => '49%',
		    'filters' => array('I18nCellFilter' => array()
                              ),
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getCurrencyList'),
		));

		$this->headers = array($header1, $header2);
	}
	
	public function getClassName() {
		return 'PayGrade';
	}

}

?>

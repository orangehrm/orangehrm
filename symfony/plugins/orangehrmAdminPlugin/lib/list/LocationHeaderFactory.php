<?php

class LocationHeaderFactory extends ohrmListConfigurationFactory {

	protected function init() {

		$header1 = new ListHeader();
		$header2 = new ListHeader();
		$header3 = new ListHeader();
		$header4 = new ListHeader();
		$header5 = new ListHeader();

		$header1->populateFromArray(array(
		    'name' => 'Name',
		    'width' => '25%',
		    'isSortable' => true,
		    'sortField' => 'name',
		    'elementType' => 'link',
		    'elementProperty' => array(
			'labelGetter' => 'getName',
			'placeholderGetters' => array('id' => 'getId'),
			'urlPattern' => 'location?locationId={id}'),
		    
		));
		
		$header2->populateFromArray(array(
		    'name' => 'City',
		    'width' => '20%',
		    'isSortable' => true,
		    'sortField' => 'city',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getCity'),
		));

		$header3->populateFromArray(array(
		    'name' => 'Country',
		    'width' => '20%',
		    'isSortable' => true,
                    'sortField' => 'countryName',
		    'filters' => array('I18nCellFilter' => array()
                              ),
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getCountryName'),
		));
		
		$header4->populateFromArray(array(
		    'name' => 'Phone',
		    'width' => '18%',
		    'isSortable' => true,
		    'sortField' => 'phone',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getPhone'),
		));
		
		$header5->populateFromArray(array(
		    'name' => 'Number of Employees',
		    'width' => '15%',
		    'isSortable' => true,
            'sortField' => 'numberOfEmployees',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getNumberOfEmployees'),
		));

		$this->headers = array($header1, $header2, $header3, $header4, $header5);
	}
	
	public function getClassName() {
		return 'Location';
	}
}
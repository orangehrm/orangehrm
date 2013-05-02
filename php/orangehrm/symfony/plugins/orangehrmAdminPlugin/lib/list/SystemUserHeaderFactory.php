<?php

class SystemUserHeaderFactory extends ohrmListConfigurationFactory {

	protected function init() {

		$header1 = new ListHeader();
		$header2 = new ListHeader();
		$header3 = new ListHeader();
        $header4 = new ListHeader();

		$header1->populateFromArray(array(
		    'name' => 'Username',
		    'width' => '33%',
		    'isSortable' => true,
		    'sortField' => 'user_name',
		    'elementType' => 'link',
		    'elementProperty' => array('labelGetter' => 'getUserName',
                                        'placeholderGetters' => array('id' => 'getId'),
                                        'urlPattern' => 'saveSystemUser?userId={id}'),
		    
		));
		
		$header2->populateFromArray(array(
		    'name' => 'User Role',
		    'width' => '20%',
		    'isSortable' => true,
		    'filters' => array('I18nCellFilter' => array()
                              ),
		    'sortField' => 'display_name',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => array('getUserRole','getName')),
		    
		));

		$header3->populateFromArray(array(
		    'name' => 'Employee Name',
		    'width' => '33%',
		    'isSortable' => true,
		    'sortField' => 'u.Employee.emp_firstname',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => array('getEmployee','getFullName')),
		    
		));
                
        $header4->populateFromArray(array(
		    'name' => 'Status',
		    'width' => '14%',
		    'isSortable' => true,
            'filters' => array('I18nCellFilter' => array()
                              ),
		    'sortField' => 'status',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getTextStatus'),
		    
		));

		$this->headers = array($header1, $header2, $header3,$header4);
	}
	
	public function getClassName() {
		return 'SystemUser';
	}

}

?>

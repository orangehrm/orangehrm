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
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getUserName'),
		    
		));
		
		$header2->populateFromArray(array(
		    'name' => 'User Type',
		    'width' => '33%',
		    'isSortable' => true,
		    'sortField' => 'user_role_id',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => array('getUserRole','getName')),
		    
		));

		$header3->populateFromArray(array(
		    'name' => 'Employee Name ',
		    'width' => '33%',
		    'isSortable' => true,
		    'sortField' => 'name',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => array('getEmployee','getFullName')),
		    
		));
                
                $header4->populateFromArray(array(
		    'name' => 'Status',
		    'width' => '33%',
		    'isSortable' => true,
		    'sortField' => 'name',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getstatus'),
		    
		));

		$this->headers = array($header1, $header2, $header3,$header4);
	}
	
	public function getClassName() {
		return 'SystemUser';
	}

}

?>

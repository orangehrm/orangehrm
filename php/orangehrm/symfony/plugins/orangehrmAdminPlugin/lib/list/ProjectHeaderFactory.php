<?php

class ProjectHeaderFactory extends ohrmListConfigurationFactory {

	protected function init() {

		$header1 = new ListHeader();
		$header2 = new ListHeader();
		$header3 = new ListHeader();

		$header1->populateFromArray(array(
		    'name' => 'Customer Name',
		    'width' => '33%',
		    'isSortable' => true,
		    'sortField' => 'name',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getCustomerName'),
		    
		));
		
		$header2->populateFromArray(array(
		    'name' => 'Project',
		    'width' => '33%',
		    'isSortable' => true,
		    'sortField' => 'name',
		    'elementType' => 'link',
		    'elementProperty' => array(
			'labelGetter' => 'getName',
			'placeholderGetters' => array('id' => 'getProjectId'),
			'urlPattern' => 'saveProject?projectId={id}'),
		));

		$header3->populateFromArray(array(
		    'name' => 'Project Admins',
		    'width' => '33%',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getProjectAdminNames'),
		));

		$this->headers = array($header1, $header2, $header3);
	}
	
	public function getClassName() {
		return 'Project';
	}

}

?>

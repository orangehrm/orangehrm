<?php

class ProjectActivityHeaderFactory extends ohrmListConfigurationFactory {
    private $allowEdit;
    
    public function setAllowEdit($allowEdit) {
        $this->allowEdit = $allowEdit;
    }

    	
	protected function init() {

		$header1 = new ListHeader();

		$header1->populateFromArray(array(
		    'name' => 'Activity Name',
		    'width' => '98%',
		    'elementType' => 'link',
		     'elementProperty' => array(
                        'linkable' => $this->allowEdit,
			'labelGetter' => 'getName',
			'urlPattern' => 'javascript:'),
		));

		$this->headers = array($header1);
	}

	public function getClassName() {
		return 'ProjectActivity';
	}
}

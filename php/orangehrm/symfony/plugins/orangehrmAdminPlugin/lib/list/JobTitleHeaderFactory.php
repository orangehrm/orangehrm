<?php

class JobTitleHeaderFactory extends ohrmListConfigurationFactory {
	
		protected function init() {

		$header1 = new ListHeader();
		$header2 = new ListHeader();

		$header1->populateFromArray(array(
		    'name' => 'Job Title',
		    'width' => '49%',
		    'isSortable' => true,
		    'sortField' => 'jobTitleName',
		    'elementType' => 'link',
		    'elementProperty' => array(
			'labelGetter' => 'getJobTitleName',
			'placeholderGetters' => array('id' => 'getId'),
			'urlPattern' => 'index.php/admin/saveJobTitle?jobTitleId={id}'),
		));

		$header2->populateFromArray(array(
		    'name' => 'Job Description',
		    'width' => '49%',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getJobDescription'),
		));

		$this->headers = array($header1, $header2);
	}

	public function getClassName() {
		return 'JobTitle';
	}
}


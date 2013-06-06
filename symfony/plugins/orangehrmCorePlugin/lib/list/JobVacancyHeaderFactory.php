<?php

class JobVacancyHeaderFactory extends ohrmListConfigurationFactory {

	protected function init() {

		$header1 = new ListHeader();
		$header2 = new ListHeader();
		$header3 = new ListHeader();
		$header4 = new ListHeader();

		$header1->populateFromArray(array(
		    'name' => 'Vacancy',
		    'width' => '32%',
		    'isSortable' => true,
		    'sortField' => 'v.name',
		    'elementType' => 'link',
		    'elementProperty' => array(
			'labelGetter' => 'getVacancyName',
			'placeholderGetters' => array('id' => 'getId'),
			'urlPattern' => 'addJobVacancy?Id={id}'),
		));

		$header2->populateFromArray(array(
		    'name' => 'Job Title',
		    'width' => '30%',
		    'isSortable' => true,
		    'sortField' => 'jt.job_title',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getJobTitleName'),
		));

		$header3->populateFromArray(array(
		    'name' => 'Hiring Manager',
		    'width' => '24%',
		    'isSortable' => true,
		    'sortField' => 'e.emp_firstname',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getHiringManagerFullName'),
		));

		$header4->populateFromArray(array(
		    'name' => 'Status',
		    'width' => '15%',
		    'isSortable' => true,
		    'sortField' => 'v.status',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getStateLabel'),
		));

		$this->headers = array($header1, $header2, $header3, $header4);
	}

	public function getClassName() {
		return 'JobVacancy';
	}

}

?>

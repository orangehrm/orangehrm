<?php

class CandidateHistoryHeaderFactory extends ohrmListConfigurationFactory {

	protected function init() {

		$headerList = array();

		for ($i = 1; $i < 5; $i++) {
			$headerList[$i] = new ListHeader();
		}

		$headerList[1]->populateFromArray(array(
		    'name' => 'Performed Date',
		    'isSortable' => false,
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getPerformedDate'),
		));

		$headerList[2]->populateFromArray(array(
		    'name' => 'Vacancy',
		    'isSortable' => false,
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getVacancyName'),
		));

		$headerList[3]->populateFromArray(array(
		    'name' => 'Description',
		    'isSortable' => false,
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getDescription'),
		));

		$headerList[4]->populateFromArray(array(
		    'name' => 'Details',
		    'isSortable' => false,
		    'elementType' => 'link',
		    'elementProperty' => array(
			'labelGetter' => 'getDetails',
			'placeholderGetters' => array('id' => 'getId'),
			'urlPattern' => 'changeCandidateVacancyStatus?id={id}'),
		));

		$this->headers = $headerList;
	}

	public function getClassName() {
		return 'CandidateHistory';
	}

}

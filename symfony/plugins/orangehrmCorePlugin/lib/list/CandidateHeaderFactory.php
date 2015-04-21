<?php

class CandidateHeaderFactory extends ohrmListConfigurationFactory {

    private static $headerInfoArray = array(
        array(
            'name' => 'Vacancy',
            'isSortable' => true,
            'sortField' => 'jv.name',
            'elementType' => 'label',
            'elementProperty' => array('getter' => 'getVacancyName'),
        ),
        array(
            'name' => 'Candidate',
            'isSortable' => true,
            'sortField' => 'jc.first_name',
            'elementType' => 'link',
            'elementProperty' => array(
                'labelGetter' => 'getCandidateName',
                'placeholderGetters' => array('id' => 'getCandidateId'),
                'urlPattern' => 'addCandidate?id={id}'),
        ),
        array(
            'name' => 'Hiring Manager',
            'isSortable' => true,
            'sortField' => 'e.emp_firstname',
            'elementType' => 'label',
            'elementProperty' => array('getter' => 'getHiringManagerName'),
        ),
        array(
            'name' => 'Date of Application',
            'isSortable' => true,
            'sortField' => 'jc.date_of_application',
            'elementType' => 'label',
            'elementProperty' => array('getter' => 'getDisplayDateOfApplication'),
        ),
        array(
            'name' => 'Status',
            'isSortable' => true,
            'filters' => array('I18nCellFilter' => array()
            ),
            'sortField' => 'jcv.status',
            'elementType' => 'label',
            'elementProperty' => array('getter' => 'getStatusName'),
        ),
        array(
            'name' => 'Resume',
            'isSortable' => false,
            'elementType' => 'link',
            'elementProperty' => array(
                'labelGetter' => 'getLink',
                'placeholderGetters' => array('id' => 'getAttachmentId'),
                'urlPattern' => 'viewCandidateAttachment?attachId={id}'),
        )
    );

	protected function init() {
		$headerList = array();
		for ($i = 1; $i < 7; $i++) {
			$headerList[$i] = new ListHeader();
            $headerList[$i]->populateFromArray(self::$headerInfoArray[$i - 1]);
		}
		$this->headers = $headerList;
	}

	public function getClassName() {
		return 'Candidate';
	}

    public static function getSortableFields() {
        return array_filter(self::$headerInfoArray, function($field) {
            return isset($field['isSortable']) && $field['isSortable'];
        });
    }

}

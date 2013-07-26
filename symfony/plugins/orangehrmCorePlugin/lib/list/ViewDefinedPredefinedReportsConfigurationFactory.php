<?php

class ViewDefinedPredefinedReportsConfigurationFactory extends ohrmListConfigurationFactory {

    private $edit;

    public function setEdit($edit) {
        $this->edit = $edit;
    }

    protected function init() {

        sfApplicationConfiguration::getActive()->loadHelpers(array('Url'));
        $header1 = new ListHeader();

        $header1->populateFromArray(array(
            'name' => 'Report Name',
            'width' => '400',
            'isSortable' => true,
            'sortField' => 'name',
            'elementType' => 'label',
            'elementProperty' => array(
                'getter' => 'getName')
        ));

        $header2 = new ListHeader();
        $header2->populateFromArray(array(
            'name' => '',
            'width' => '95',
            'isSortable' => false,
            'elementType' => 'link',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'label' => __('Run'),
                'placeholderGetters' => array('id' => 'getReportId'),
                'urlPattern' => url_for('core/displayPredefinedReport') . '?reportId={id}'
            ),
        ));


        $header3 = new ListHeader();
        $header3->populateFromArray(array(
            'name' => '',
            'width' => '95',
            'isSortable' => false,
            'elementType' => 'link',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'label' => __('Edit'),
                'placeholderGetters' => array('id' => 'getReportId'),
                'urlPattern' => url_for('core/definePredefinedReport') . '?reportId={id}'
            ),
        ));

        if ($this->edit) {
            $this->headers = array($header1, $header2, $header3);
        } else {
            $this->headers = array($header1, $header2);
        }
    }

    public function getClassName() {
        return 'ViewPredefinedReport';
    }

}


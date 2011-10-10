<?php

class ViewPredefinedReportsConfigurationFactory extends ohrmListConfigurationFactory {

    protected function init() {
        $header1 = new ListHeader();
        sfApplicationConfiguration::getActive()->loadHelpers(array('Url'));

        $header1->populateFromArray(array(
            'name' => 'Report Name',
            'width' => '600',
            'isSortable' => true,
            'sortField' => 'name',
            'elementType' => 'link',
            'elementProperty' => array(
                'labelGetter' => 'getName',
                'placeholderGetters' => array('id' => 'getReportId'),
                'urlPattern' => url_for('core/displayPredefinedReport') . '?reportId={id}'),
        ));

        $this->headers = array($header1);
    }

    public function getClassName() {
        return 'PredefinedReport';
    }

}


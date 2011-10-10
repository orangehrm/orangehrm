<?php

class ViewDefinedPredefinedReportsConfigurationFactory extends ohrmListConfigurationFactory {
    protected function init() {
        
        sfApplicationConfiguration::getActive()->loadHelpers(array('Url'));
        $header1 = new ListHeader();

        $header1->populateFromArray(array(
            'name' => 'Report Name',
            'width' => '600',
            'isSortable' => true,
            'sortField' => 'name',
            'elementType' => 'link',
            'elementProperty' => array(
                'labelGetter' => 'getName',
                'placeholderGetters' => array('id' => 'getReportId'),
                'urlPattern' =>  url_for('core/definePredefinedReport') . '?reportId={id}'),
        ));

        $this->headers = array($header1);
    }

    public function getClassName() {
        return 'ViewPredefinedReport';
    }
}


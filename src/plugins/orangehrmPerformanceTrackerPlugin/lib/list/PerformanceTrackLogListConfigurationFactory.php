<?php

class PerformanceTrackerLogListConfigurationFactory extends ohrmListConfigurationFactory {

    protected static $listMode;
    protected static $loggedInEmpNumber;
    protected $title;


    public function __construct($customTitle) {
        $this->title = $customTitle;
    }
    
    protected function init() {
        
        $this->setRuntimeDefinitions(array('title'=> $this->title));
        $header1 = new PerformanceTrackerLogListHeader();
        $header2 = new ListHeader();
        $header3 = new ListHeader();
        $header4 = new ListHeader();
        $header5 = new ListHeader();
        $header6 = new ListHeader();

        $header1->populateFromArray(array(
            'name' => 'Reviewer',
            'width' => '18%',
            'elementType' => 'performanceTrackerLogLink',
            'elementProperty' => array(
                'labelGetter' => 'getReviewerName', 
                'placeholderGetters' => array('trackId' => 'getPerformanceTrackId', 'id' => 'getId'),
                'urlPattern' => 'index.php/performance/addPerformanceTrackerLog/trackId/{trackId}/logId/{id}'),
        ));

        $header2->populateFromArray(array(
            'name' => 'Log',
            'width' => '20%',
            'elementType' => 'label',
            'elementProperty' => array('getter' => 'getLog'),
        ));
                
          $header3->populateFromArray(array(
            'name' => 'Comments',
            'width' => '30%',
            'isSortable' => false,
            'elementType' => 'comment',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'getter' => 'getComment',
                'idPattern' => 'hdnTrackLogComment-{id}',
                'namePattern' => 'trackLogComments[{id}]',
                'placeholderGetters' => array('id' => 'getId'),
                'hasHiddenField' => true,
                'hiddenFieldName' => 'trackLog[{id}]',
                'hiddenFieldId' => 'hdnTrackLog_{id}',
                'hiddenFieldValueGetter' => 'getId',
            ),
        ));
             
        $header4->populateFromArray(array(
            'name' => 'Achievement',
            'elementType' => 'label',
            'elementProperty' => array('getter' => 'getAchievementText'),
        ));

        $header5->populateFromArray(array(
            'name' => 'Added Date',
            'elementType' => 'labelDate',
            'elementProperty' => array('getter' => 'getAddedDate'),
        ));

        $header6->populateFromArray(array(
            'name' => 'Modified Date',
            'elementType' => 'labelDate',
            'elementProperty' => array('getter' => 'getModifiedDate'),
        ));

        $this->headers = array($header1, $header2, $header3, $header4, $header5, $header6);
    }

    public function getClassName() {
        return 'PerformanceTrackerLog';
    }

    public static function setLoggedInEmpNumber($empNumber) {
        self::$loggedInEmpNumber = $empNumber;
    }

}

?>

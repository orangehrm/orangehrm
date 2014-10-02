<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of addReviewForm
 *
 * @author indiran
 */
class AddPerformanceTrackerLogForm extends sfForm {

    public $performanceTrackerService;

    public function getPerformanceTrackerService() {
        if (is_null($this->performanceTrackerService)) {
            $this->performanceTrackerService = new PerformanceTrackerService();
        }
        return $this->performanceTrackerService;
    }

    public function configure() {
        $achievement = array(1 => 'Positive', 2 => 'Negative');
        $this->setWidgets(array(
            //TO DO remove name  
            'log' => new sfWidgetFormInput(),
            'achievement' => new sfWidgetFormSelect(array('choices' => $achievement)),
            'comment' => new sfWidgetFormTextarea(),
            'hdnTrckId' => new sfWidgetFormInputHidden(),
            'hdnLogId' => new sfWidgetFormInputHidden(),
            'hdnMode' => new sfWidgetFormInputHidden(),
        ));

        $this->setValidators(array(
            'log' => new sfValidatorString(array('required' => true)),
            'achievement' => new sfValidatorChoice(array('choices' => array_keys($achievement))),
            'comment' => new sfValidatorString(array('required' => true, 'max_length'=> 3000)),
            'hdnTrckId' => new sfValidatorString(array('required' => false)),
            'hdnLogId' => new sfValidatorString(array('required' => false)),
            'hdnMode' => new sfValidatorString(array('required' => false))
        ));

        $this->widgetSchema->setNameFormat('addperformanceTrackerLog[%s]');
        $this->widgetSchema->setLabels(array('log' => __('log'). ' <em>*</em>',
            'comment' => __("Comment"). ' <em>*</em>'));
    }

    public function getPerformanceTrackerLog() {

        $performanceTrackerLog = new PerformanceTrackerLog();
        $performanceTrackerLog->setPerformanceTrackId($this->getValue('hdnTrckId'));
         
        //die;
        $logId = $this->getValue('hdnLogId');
        if (!empty($logId)) {
            $performanceTrackerLog->setId($logId);
        }
        $performanceTrackerLog->setComment($this->getValue('comment'));
        $performanceTrackerLog->setLog($this->getValue('log'));
        $performanceTrackerLog->setAchievement($this->getValue('achievement'));
        return $performanceTrackerLog;
    }

    public function setDefaultValues($trackId, $trackLogId) {
        if (!empty($trackLogId)) { 
            $performanceTrackLog = $this->getPerformanceTrackerService()->getPerformanceTrackerLog($trackLogId);
            if ($performanceTrackLog instanceof PerformanceTrackerLog) {
                $this->setDefault('hdnTrckId', $performanceTrackLog->getPerformanceTrackId());
                $this->setDefault('hdnLogId', $performanceTrackLog->getId());
                $this->setDefault('log', $performanceTrackLog->getLog());
                $this->setDefault('comment', $performanceTrackLog->getComment());
                $this->setDefault('achievement', $performanceTrackLog->getAchievement());
                $this->setDefault('hdnMode', 'edit');
            }
        } elseif (!empty($trackId)) { 
            //TO DO chechk if track exist 
            $this->setDefault('hdnTrckId', $trackId);
        }
    }

}

?>

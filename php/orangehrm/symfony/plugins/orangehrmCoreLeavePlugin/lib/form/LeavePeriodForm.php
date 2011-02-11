<?php
/**
 * Form class to define leave period
 */
class LeavePeriodForm extends sfForm {

	public function configure() {
        
		$leavePeriodService = new LeavePeriodService();
        $month = "-- " . __("Month") . " --";
        $date = "-- " . __("Date") . " --";
		$monthsChoiceList = array($month);
		$monthsChoiceList = array_merge($monthsChoiceList, $leavePeriodService->getListOfMonths());
		$datesChoiceList = array($date);
			
		$currentLeavePeriod = $leavePeriodService->getCurrentLeavePeriod();
		if (!is_null($currentLeavePeriod)) {
			$datesChoiceList = array_merge($datesChoiceList, $leavePeriodService->getListOfDates($currentLeavePeriod->getStartMonthValue()));
		}

		$this->setWidgets(array(
            'cmbStartMonth' => new sfWidgetFormSelect(array(
            	'choices' => $monthsChoiceList,
		), array(
            	'class' => 'formSelect'
            	)),
            'cmbStartDate' => new sfWidgetFormSelect(array(
            	'choices' => $datesChoiceList,
            	),array(
            	'class' => 'formSelect',
            	'style' => 'width: auto'
            	)),
            'cmbStartMonthForNonLeapYears' => new sfWidgetFormSelect(array(
            	'choices' => $monthsChoiceList,
                'default' => 2
            	), array(
            	'class' => 'formSelect'
            	)),
            'cmbStartDateForNonLeapYears' => new sfWidgetFormSelect(array(
            	'choices' => array(),
            	),array(
            	'class' => 'formSelect',
            	'style' => 'width: auto'
            	)),
          

            	));
         $this->setValidators(array(
                'cmbStartMonth' => new sfValidatorString(array('required' => false)),
                'cmbStartDate' => new sfValidatorString(array('required' => false)),
                'cmbStartMonthForNonLeapYears' => new sfValidatorString(array('required' => false)),
                'cmbStartDateForNonLeapYears' => new sfValidatorString(array('required' => false)),
                
        ));

        $this->widgetSchema->setNameFormat('leaveperiod[%s]');
	}
    
        }

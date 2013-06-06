<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2010 OrangeHRM Inc., http://www.orangehrm.com
 *
 * Please refer the file license/LICENSE.TXT for the license which includes terms and conditions on using this software.
 *
 * */

/**
 * Description of ohrmWidgetFormLeavePeriod
 *
 * @author samantha
 */
class ohrmWidgetFormLeavePeriod  extends sfWidgetForm {
    /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * from_label:  The label for the from date widget
   *  * to_label:    The label for the to date widget
   * 
   *  * from_label_template: The template used to render label for from date widget
   *                 Available placeholders: %from_id%, %from_label%
   *  * to_label_template: The template used to render label for to date widget
   *                 Available placeholders: %to_id%, %to_label%
   * 
   *  * template:    The template to use to render the widget
   *                 Available placeholders: %from_date%, %to_date% %from_label% %to_label%
   *
   *  also see options in sfWidgetFormDateRange
   * 
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see ohrmWidgetFormDateRange
   */
     protected $leaveTypeService;
     
    public function getLeavePeriodService() {
        if (!isset($this->leavePeriodService)) {
            $this->leavePeriodService = new LeavePeriodService();
        }        
        return $this->leavePeriodService;
    }

    public function setLeavePeriodService($leavePeriodService) {
        $this->leavePeriodService = $leavePeriodService;
    }
    
  protected function configure($options = array(), $attributes = array()) {
    parent::configure($options, $attributes);
    
    $this->addOption('from_date');
    $this->addOption('to_date');
    
    if( LeavePeriodService::getLeavePeriodStatus() == LeavePeriodService::LEAVE_PERIOD_STATUS_FORCED){
        $this->addOption('choices', null);
        $this->addOption('from_label', '');
        $this->addOption('to_label', '');
        $this->addOption('leave_period', '');
        
        $this->addOption('from_label_template', "");
        $this->addOption('to_label_template', "");
        
        $this->addOption('template', '%leave_period% %from_date%  %to_date%');
        
        $this->setOption('from_date', new sfWidgetFormInputHidden(array(), array('id' => 'date_from')));
        $this->setOption('to_date', new sfWidgetFormInputHidden(array(), array('id' => 'date_to')));
        $this->setOption('leave_period', new sfWidgetFormChoice(array('choices' => array()),array('id' => 'period')));
        
    }else{
        $this->addOption('from_label', '');
        $this->addOption('to_label', 'to');

        $this->addOption('from_label_template', "<label for='%from_id%' class='date_range_label'>%from_label%</label>");
        $this->addOption('to_label_template', "<label for='%to_id%' class='date_range_label'>%to_label%</label>");

        $this->addOption('template', '%from_label% %from_date% %to_label% %to_date%');

        $this->setOption('from_date', new ohrmWidgetDatePicker(array(), array('id' => 'date_from')));
        $this->setOption('to_date', new ohrmWidgetDatePicker(array(), array('id' => 'date_to')));
    }
    
    
  }    
  
  /**
   * Renders the widget.
   *
   * @param  string $name        The element name
   * @param  string $value       The date displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array()) {
    $value = array_merge(array('from' => '', 'to' => ''), is_array($value) ? $value : array());
    if($value['from']=='' && $value['to'] == ''){
        // If leave period defined, use leave period start and end date
        $leavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriodByDate(date('Y-m-d', time()));
        
        $value['from']  = $leavePeriod[0];
        $value['to']   = $leavePeriod[1];
    }
    $fromWidget = $this->getOption('from_date');
    $fromId = $fromWidget->getAttribute('id');
    if (empty($fromId)) {
        $fromId = $this->generateId($name . '_from');
        $fromWidget->setAttribute('id', $fromId);
    }
    $toWidget = $this->getOption('to_date');                
    $toId = $toWidget->getAttribute('id');   
    if (empty($toId)) {
        $toId = $this->generateId($name . '_to');
        $toWidget->setAttribute('id', $toId);
    }    
    
    $fromLabelHtml = '';
    $fromLabel = $this->getOption('from_label');
    if (!empty($fromLabel)) {
        
       
        $fromLabelHtml = strstr($this->getOption('from_label_template'), array(
            '%from_id%' => $fromId,
            '%from_label%' => $this->translate($fromLabel)
        ));
    }
    
    $toLabel = $this->getOption('to_label');
    $toLabelHtml = '';
    if (!empty($toLabel)) {

        $toLabelHtml = strtr($this->getOption('to_label_template'), array(
            '%to_id%' => $toId,
            '%to_label%' => $this->translate($toLabel)
        ));

    }    
    
    if( LeavePeriodService::getLeavePeriodStatus() == LeavePeriodService::LEAVE_PERIOD_STATUS_FORCED){
        $choices = is_array($this->getOption('choices'))?$this->getOption('choices'):$this->getLeavePeriodList();
        $leavePeriodWidget = $this->getOption('leave_period');
        $leavePeriodWidget->setOption('choices',$choices);
        $html = strtr($this->translate($this->getOption('template')), array(
          '%leave_period%'=> $leavePeriodWidget->render(null),
          '%from_date%' => $fromWidget->render($name.'[from]', $value['from'],array('id' => 'date_from')),
          '%to_date%' => $toWidget->render($name.'[to]', $value['to'],array('id' => 'date_to')),
        ));
        
                $javaScript = sprintf(<<<EOF
 <script type="text/javascript">

   

    $(document).ready(function(){
        var intialFrom  = $('#date_from').val();
        var intialTo    = $('#date_to').val();
        
        $('#period').val(intialFrom+'$$'+intialTo);
        
        $('#period').change(function() {
            var val = $(this).val();
            
            if (typeof val == 'string') {
                var selectValue = val.split('$$');
                $('#date_from').val(selectValue[0]);
                $('#date_to').val(selectValue[1]);
            } else {
                $('#date_from').val('');
                $('#date_to').val('');                        
            }
          });
        
    });

</script>
EOF
                        
                        
        );
        
        $html .= $javaScript;
    }else{
        $html = strtr($this->translate($this->getOption('template')), array(
          '%from_label%' => $fromLabelHtml,
          '%to_label%' => $toLabelHtml,
          '%from_date%' => $fromWidget->render($name.'[from]', $value['from'], array('id' => 'date_from')),
          '%to_date%' => $toWidget->render($name.'[to]', $value['to'], array('id' => 'date_to')),
        ));
    }
    
    return $html;
  } 
  
    /**
   * Gets the stylesheet paths associated with the widget.
   *
   * @return array An array of stylesheet paths
   */
  public function getStylesheets()
  {
    return array_unique(array_merge($this->getOption('from_date')->getStylesheets(), $this->getOption('to_date')->getStylesheets()));
  }

  /**
   * Gets the JavaScript paths associated with the widget.
   *
   * @return array An array of JavaScript paths
   */
  public function getJavaScripts()
  {
    return array_unique(array_merge($this->getOption('from_date')->getJavaScripts(), $this->getOption('to_date')->getJavaScripts()));
  }
  
  /**
   * Get Leave Period List
   * @return string
   */
  public function getLeavePeriodList(){
      $choices = array();
      $leavePeriodService = new LeavePeriodService();
      $leavePeriodList = $leavePeriodService->getGeneratedLeavePeriodList();
      
      foreach( $leavePeriodList as $leavePeriod){
          $choices[set_datepicker_date_format($leavePeriod[0]).'$$'.set_datepicker_date_format($leavePeriod[1])] = set_datepicker_date_format($leavePeriod[0]).' - '. set_datepicker_date_format($leavePeriod[1]);
      }
      
      return $choices;
  }
  
}

?>

<?php

use_javascripts_for_form($form);
use_stylesheets_for_form($form);
use_stylesheet(plugin_web_path('orangehrmLeavePlugin', 'css/viewLeaveBalanceReport'));
?>


<?php if ($form->hasErrors()): ?>
    <div class="messagebar">
        <?php include_partial('global/form_errors', array('form' => $form)); ?>
    </div>
<?php endif; ?>
<div class="box searchForm" id="leave-balance-report">
    <div class="head">
        <h1><?php echo ($mode == 'my') ? __("My Leave Entitlements and Usage Report") : __("Leave Entitlements and Usage Report");?></h1>
    </div>
    <div class="inner">
        <?php include_partial('global/flash_messages'); ?>
        <?php if (!isset($hide_form)): ?>
        <form id="frmLeaveBalanceReport" name="frmLeaveBalanceReport" method="post" 
              action="">

            <fieldset>                
                <ol>
                    <?php echo $form->render(); ?>
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>                    
                </ol>                   
                <p>
                    <input type="button" name="view" id="viewBtn" value="<?php echo __('View');?>"/>
                    <?php include_component('core', 'ohrmPluginPannel', array('location' => 'search_form_buttons_section')); ?>
                </p>
            </fieldset>
        </form>
        <?php endif;?>
    </div> <!-- inner -->    
</div> 

<?php if (!empty($resultsSet)) { ?>
    <div id="report-results" class="box noHeader">
        <div class="inner">
            <?php if ($pager->haveToPaginate()):?>
            <div class="top">
                <?php include_partial('core/report_paging', array('pager' => $pager));?>                
            </div>
            <?php endif; ?> 
            <table class="table nosort" cellspacing="0" cellpadding="0">

            <?php $headers = $sf_data->getRaw('tableHeaders');
                  $headerInfo = $sf_data->getRaw('headerInfo');?>

                <thead class="fixedHeader">
                <tr class="heading">
                    <?php 
                          foreach($headers as $mainHeader => $subHeaders):  
                              $subHead = array_shift($subHeaders);
                    ?>                      
                    <th class="header" colspan="<?php echo count($subHeaders);?>" style="text-align: center;"><?php echo __($subHead);?></th>
                    <?php endforeach;?>
                </tr>
                <tr class="subHeading">
                    <?php $i = 0; foreach($headers as $subHeaders): array_shift($subHeaders);?>

                            <?php foreach($subHeaders as $subHeader):?>
                    <th class="header" style="text-align: center;" ><?php echo __($subHeader);?></th>
                            <?php endforeach;?>                    
                    <?php endforeach;?>
                </tr>
                </thead>
                <?php                
                    $reportBuilder = new ReportBuilder();
                    $linkParamsRaw = $sf_data->getRaw('linkParams');
                    $rowCssClass = "even";
                    $results = $sf_data->getRaw('resultsSet');?>                
                <tbody class="scrollContent"> 
                <?php foreach ($results as $row):      
                    
                        $rowCssClass = ($rowCssClass === 'odd') ? 'even' : 'odd';?>                      
                <tr class="<?php echo $rowCssClass;?>">
                <?php foreach ($row as $key => $column):                            
                         $info = $headerInfo[$key];
                         $tdClass = !empty($info['align']) ? " class='{$info['align']}'" : '';
                         if(is_array($column)):
                            foreach ($column as $colKey => $colVal):
                                $headInf = $info[$colKey];                                                                            
                                if(($headInf["groupDisp"] == "true") && ($headInf["display"] == "true")):?>
                                    <!--<td><table>-->
                                    <td><ul>                                      
                                        <ul>                                         
                                        <?php foreach($colVal as $data):?>
                                               <!--<tr style="height: 10px;"><td headers="10"><?php // echo __($data);?></td></tr>-->                                               
                                               <li><?php echo esc_specialchars(__($data));?></li>                                        
                                        <?php endforeach;?>
                                        </ul>                                    
                                     </td>
                                     <!--</table></td>-->
                            <?php endif;                                                                                      
                             endforeach;
                         else:
                             //echo $key . '-' . $column;
                            if(($info["groupDisp"] == "true") && ($info["display"] == "true")):?>
                            <td<?php echo $tdClass;?>>
                          <?php if (($column == "") || is_null($column)) {
                                    $column = "0.00";
                                }
                                    
                                if (isset($info['link'])) {
                                    $mergedLinkParams = array_merge($linkParamsRaw, $row);

                                    $link = $info['link'];
                                    if ($mode == 'my') {
                                        $link = str_replace('viewLeaveList', 'viewMyLeaveList', $link);
                                        $link = str_replace('viewLeaveEntitlements', 'viewMyLeaveEntitlements', $link);
                                    }
                                    $linkParts = explode('/', $link, 2);
                                    $module = $linkParts[0];
                                    $action = strstr($linkParts[1], '?', true);
                                    if ($action == false) {
                                        $action = strstr($linkParts[1], '/', true);
                                    }
                                    if ($action == false) {
                                        $action = $linkParts[1];
                                    }
                                    $permissions = $sf_context->getUserRoleManager()->getScreenPermissions($module, $action);
                                    if ($permissions->canRead()) {
                                        $url = $reportBuilder->replaceHeaderParam($link, $mergedLinkParams);
                                        echo link_to(esc_specialchars(__($column)), $url);
                                    } else {
                                        echo esc_specialchars(__($column));
                                    }

                                } else {
                                    echo esc_specialchars(__($column));

                                };                                    
                                
                                ?></td>
                      <?php else: ?>
                            <input type="hidden" name="<?php echo $key;?>[]" value="<?php echo $column;?>"/>
                      <?php endif;
                         endif;?>                            
                 <?php endforeach;?>
                 </tr>             
                 <?php endforeach;?>
                </tbody>
            </table>
            <?php if ($pager->haveToPaginate()):?>
            <div class="bottom">
                <?php include_partial('core/report_paging', array('pager' => $pager));?>                
            </div>
            <?php endif; ?>             
        </div>    
    </div>
<?php } ?>

<script type="text/javascript">
    var employeeReport = <?php echo LeaveBalanceReportForm::REPORT_TYPE_EMPLOYEE;?>;
    var leaveTypeReport = <?php echo LeaveBalanceReportForm::REPORT_TYPE_LEAVE_TYPE;?>;
    
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var displayDateFormat = '<?php echo str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())); ?>';
    var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
    var lang_dateError = '<?php echo __("To date should be after from date") ?>';    
    
    function submitPage(pageNo) {
        var actionUrl = $('#frmLeaveBalanceReport').attr('action') + '?pageNo=' + pageNo;
        $('#frmLeaveBalanceReport').attr('action', actionUrl).submit(); 
    }
    
    function toggleReportType(reportType) {
        
        var reportType = $("#leave_balance_report_type").val();
        var reportTypeLi = $('#leave_balance_leave_type').parent('li');
        var employeeNameLi = $('#leave_balance_employee_empName').parent('li');
        var dateLi = $('#date_from').parent('li');
        var jobTitleLi = $('#leave_balance_job_title').parent('li');
        var locationLi = $('#leave_balance_location').parent('li');
        var subUnitLi = $('#leave_balance_sub_unit').parent('li');
        var terminatedLi = $('#leave_balance_include_terminated').parent('li');
        
        var viewBtn = $('#viewBtn');

        if (reportType == employeeReport) {
            reportTypeLi.hide();
            employeeNameLi.show(); 
            dateLi.show();
            jobTitleLi.hide();
            locationLi.hide();
            subUnitLi.hide();
            terminatedLi.hide();
            viewBtn.show();
           
        } else if (reportType == leaveTypeReport) {
            reportTypeLi.show();
            employeeNameLi.hide();           
            jobTitleLi.show();
            locationLi.show();
            subUnitLi.show();
            terminatedLi.show();            
            dateLi.show();            
            viewBtn.show();
        } else {
            reportTypeLi.hide();
            employeeNameLi.hide();                    
            dateLi.hide();
            jobTitleLi.hide();
            locationLi.hide();
            subUnitLi.hide();
            terminatedLi.hide();
            viewBtn.hide();                        
        }        
                    
        var reportTypeWidget = $("#leave_balance_report_type");
        var empNameWidget = $("#leave_balance_employee_empName");
        empNameWidget.innerWidth(reportTypeWidget.innerWidth());        
    }   
   
    $(document).ready(function() {        
        
        $('a.total').live('click', function(){
            
        });
        
        <?php if ($mode != 'my') { ?>
        toggleReportType();
        <?php } ?>       
        
        $('#report-results table.table thead.fixedHeader tr:first').hide();
        
        $('#viewBtn').click(function() {       
            $('#frmLeaveBalanceReport input.inputFormatHint').val('');
            $('#frmLeaveBalanceReport input.ac_loading').val('');        
            $('#frmLeaveBalanceReport').submit();
        });
        
        $("#leave_balance_report_type").change(function() {          
            toggleReportType();
        });
        
        $.validator.addMethod("checkEmployeeNameNotChanged", function(value, element, params) {

            var isValid = true;

            var idField = $('#leave_balance_employee_empId');
            if (idField.val() !== '') {
                var inputFieldName = $('#leave_balance_employee_empName').val();
                var lastSelectedName = idField.data('item.name');

                isValid = ($.trim(inputFieldName) === $.trim(lastSelectedName));
            }

            return isValid;
        });        
        
        $.validator.addMethod("triggerEmpIdValidation", function(value, element, params) {        
           $('#leave_balance_employee_empId').valid();
           return true;
        }); 

        $('#leave_balance_employee_empName').result(function(event, item) {
           $('#leave_balance_employee_empId').val(item.id)
               .data('item.name', item.name)
               .valid();
        });        
        
        $('#frmLeaveBalanceReport').validate({
                ignore: [],    
                rules: {
                    'leave_balance[employee][empName]': {
                        triggerEmpIdValidation: true,
                        onkeyup: 'if_invalid'
                    },                    
                    'leave_balance[employee][empId]': {
                        required: function(element) {
                            return $("#leave_balance_report_type").val() == employeeReport;
                        },
                        checkEmployeeNameNotChanged: true
                    },
                    'leave_balance[leave_type]':{required: function(element) {
                            return $("#leave_balance_report_type").val() == leaveTypeReport;
                        } 
                    },
                    'leave_balance[date][from]': {
                        required: true,
                        valid_date: function() {
                            return {
                                required: true,                                
                                format:datepickerDateFormat,
                                displayFormat:displayDateFormat
                            }
                        }
                    },
                    'leave_balance[date][to]': {
                        required: true,
                        valid_date: function() {
                            return {
                                required: true,
                                format:datepickerDateFormat,
                                displayFormat:displayDateFormat
                            }
                        },
                        date_range: function() {
                            return {
                                format:datepickerDateFormat,
                                displayFormat:displayDateFormat,
                                fromDate:$("#date_from").val()
                            }
                        }
                    }
                    
                },
                messages: {
                    'leave_balance[employee][empId]':{
                        required:'<?php echo __(ValidationMessages::REQUIRED); ?>',
                        checkEmployeeNameNotChanged:'<?php echo __(ValidationMessages::INVALID); ?>'
                    },
                    'leave_balance[leave_type]':{
                        required:'<?php echo __(ValidationMessages::REQUIRED); ?>'
                    },
                    'leave_balance[date][from]':{
                        required:lang_invalidDate,
                        valid_date: lang_invalidDate
                    },
                    'leave_balance[date][to]':{
                        required:lang_invalidDate,
                        valid_date: lang_invalidDate ,
                        date_range: lang_dateError
                    }                  
            }

        });        

    });

</script>



<?php use_javascript(plugin_web_path('orangehrmTimePlugin', 'js/viewEmployeeTimesheet')); ?>
<?php if($timesheetPermissions->canRead()){?>
<div class="box">
    <div class="head">
        <h1><?php echo __("Select Employee"); ?></h1>
    </div>
    <div class="inner">
        <form action="<?php echo url_for("time/viewEmployeeTimesheet"); ?>" id="employeeSelectForm" 
              name="employeeSelectForm" method="post">
                  <?php echo $form->renderHiddenFields(); ?>
            <fieldset>
                <ol>
                    <li>
                        <?php echo $form['employeeName']->renderLabel(__('Employee Name') . ' <em>*</em>'); ?>
                        <?php echo $form['employeeName']->render(); ?>
                        <?php echo $form['employeeName']->renderError(); ?>
                    </li>
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
                <p>
                    <input type="button" class="" id="btnView" value="<?php echo __('View') ?>" />
                </p>
            </fieldset>
        </form>
    </div>
</div>

<!-- Employee-pending-submited-timesheets -->
<?php if (!($pendingApprovelTimesheets == null)): ?>
    <div class="box ">

        <div class="head">
            <h1><?php echo __("Timesheets Pending Action"); ?></h1>
        </div>

        <div class="inner">
            <form action="<?php echo url_for("time/viewPendingApprovelTimesheet"); ?>" id="viewTimesheetForm" method="post" >        
                <table class="table">
                    <thead>
                        <tr>
                            <th id="tablehead" style="width:40%"><?php echo __('Employee name'); ?></th>
                            <th id="tablehead" style="width:54%"><?php echo __('Timesheet Period'); ?></th>
                            <th style="width:6%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        foreach ($sf_data->getRaw('pendingApprovelTimesheets') as $pendingApprovelTimesheet):
                            ?>
                            <tr class="<?php echo ($i & 1) ? 'even' : 'odd'; ?>">
                        <input type="hidden" name="timesheetId" value="<?php echo $pendingApprovelTimesheet['timesheetId']; ?>" />
                        <input type="hidden" name="employeeId" value="<?php echo $pendingApprovelTimesheet['employeeId']; ?>" />
                        <input type="hidden" name="startDate" value="<?php echo $pendingApprovelTimesheet['timesheetStartday']; ?>" />
                        <td>
                            <?php echo htmlspecialchars($pendingApprovelTimesheet['employeeFirstName']) . " " . htmlspecialchars($pendingApprovelTimesheet['employeeLastName']); ?>
                        </td>
                        <td>
                            <?php echo set_datepicker_date_format($pendingApprovelTimesheet['timesheetStartday']) . " " . __("to") . " " . set_datepicker_date_format($pendingApprovelTimesheet['timesheetEndDate']) ?>
                        </td>
                        <td align="center" class="<?php echo $pendingApprovelTimesheet['timesheetId'] . "##" . $pendingApprovelTimesheet['employeeId'] . "##" . $pendingApprovelTimesheet['timesheetStartday'] ?>">
                            <a href="<?php
                    echo 'viewPendingApprovelTimesheet?timesheetId=' .
                    $pendingApprovelTimesheet['timesheetId'] . '&employeeId=' .
                    $pendingApprovelTimesheet['employeeId'] . '&timesheetStartday=' .
                    $pendingApprovelTimesheet['timesheetStartday'];
                            ?>" id="viewSubmitted">
                                <?php echo __("View"); ?>
                            </a>
                        </td>
                        </tr>                        
                        <?php
                        $i++;
                    endforeach;
                    ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
<?php endif; ?>
<?php }?>
<script type="text/javascript">
    var employees = <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()) ?> ;
    var employeesArray = eval(employees);
    var errorMsge;
    var lang_typeForHints = '<?php echo __("Type for hints") . '...'; ?>';
    var time_EmployeeNameRequired   = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var time_ValidEmployee          = '<?php echo __(ValidationMessages::INVALID); ?>';
    
    $(document).ready(function() {
        $("#employee").autocomplete(employees, {
            formatItem: function(item) {
                return $('<div/>').text(item.name).html();
            },
            formatResult: function(item) {
                return item.name
            }  
            ,matchContains:true
        }).result(function(event, item) {
            $("#employee").valid();
        });
        
        $("#employeeSelectForm").validate({

            rules: {
                'time[employeeName]' : {
                    required:true,
                    maxlength: 200,
                    validEmployeeName: true,
                    onkeyup: false
                }
            },
            messages: {
                'time[employeeName]' : {
                    required: time_EmployeeNameRequired,
                    validEmployeeName: time_ValidEmployee
                }
            }

        });

        $('#viewSubmitted').click(function() {
            var data = $(this).parent().attr("class").split("##");
            // var ids = ($(this).attr("id")).split("_");
            var url = 'viewPendingApprovelTimesheet?timesheetId='+data[0]+'&employeeId='+data[1]+'&timesheetStartday='+data[2];
            $(location).attr('href',url);
        });    
        
        $('#btnView').click(function() {
            if($("#employee").val() == lang_typeForHints) {
                $("#employee").val('');
            }
            $('#employeeSelectForm').submit();
        });
        
        $.validator.addMethod("validEmployeeName", function(value, element) {                

            return autoFill('employee', 'time_employeeId', employees);                 
        });
    });
    
    function autoFill(selector, filler, data) {
        $("#" + filler).val("");
        var valid = false;
        $.each(data, function(index, item){
            if(item.name.toLowerCase() == $("#" + selector).val().toLowerCase()) {
                $("#" + filler).val(item.id);
                valid = true;
            }
        });
        return valid;
    }
    
</script>


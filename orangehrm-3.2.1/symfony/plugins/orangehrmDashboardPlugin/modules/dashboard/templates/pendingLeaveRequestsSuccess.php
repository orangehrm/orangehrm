<?php $recCount = count($leaveList); ?>
<div id="task-list-group-panel-container" class="" style="height:100%; ">
    <div id="task-list-group-panel-menu_holder" class="task-list-group-panel-menu_holder" style="height:85%; overflow-x: hidden; overflow-y: auto;">
        <table class="table hover">
            <tbody>
                <?php
                if ($recCount > 0):
                    $count = 0;
                    foreach ($leaveList as $key => $leaveData):
                        ?>
                        <tr class="<?php echo ($count & 1) ? 'even' : 'odd' ?>">
                            <td>
                                <a href="<?php echo url_for('leave/viewLeaveRequest') . '/id/' . $leaveData['leaveRequestId'] ?>">
                                    <?php
                                    $count++;
                                    echo str_pad($count, 2, '0', STR_PAD_LEFT) . ". " . $leaveData['firstName'] . " " . $leaveData['lastName'] . " " . set_datepicker_date_format($leaveData['dateApplied']);
                                    ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>                    
                <?php else: ?>
                        <tr class="odd"><td><?php echo __(DashboardService::NO_REC_MESSAGE); ?></td></tr>
                <?php endif; ?>
            </tbody>  
        </table>
    </div>
    <div id="total" >
        <table class="table">
            <tr class="total">
                <td style="text-align:left;padding-left:20px; cursor: pointer"> 
                    <?php
                    $date = pendingLeaveRequestsAction::getDateRange();
                    $dateRange = set_datepicker_date_format($date->getFromDate()) .' '. __('to') . ' ' .set_datepicker_date_format($date->getToDate());
                    echo ' <span title = "' . $dateRange . '">' . __('%months% month(s)', array('%months%' => 3)) . '</span>';
                    ?>
                </td>
                <td style="text-align:right;padding-right:20px;"> 
                    <?php
                    echo __('Total : ') . $recCount . ' / ' . $recordCount;
                    ?>
                </td>                
            </tr>
        </table>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // hover color change effect
        $("#task-list-group-panel-slider li").hover(function() {
            $(this).animate({opacity: 0.90}, 100, function(){ 
                $(this).animate({opacity: 1}, 0);
            } );
        });     
    });

</script>
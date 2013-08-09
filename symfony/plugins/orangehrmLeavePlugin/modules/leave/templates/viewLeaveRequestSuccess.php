<?php use_javascript(plugin_web_path('orangehrmLeavePlugin', 'js/viewLeaveRequestSuccess.js'));?>

<?php if($leaveListPermissions->canRead()){?>
<div id="processing"></div>

<!--this is ajax message place -->
<div id="msgPlace"></div>
<!-- end of ajax message place -->

<?php include_component('core', 'ohrmList', array('requestComments' => $requestComments)); ?>
<input type="hidden" name="hdnMode" value="<?php echo $mode; ?>" />

<!-- comment dialog -->
<div class="modal midsize hide" id="commentDialog">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo __('Leave Comments'); ?></h3>
  </div>
  <div class="modal-body">
    <p>
    <form action="updateComment" method="post" id="frmCommentSave">
        <input type="hidden" id="leaveId" />
        <input type="hidden" id="leaveOrRequest" />        
        <?php echo $leavecommentForm ?>
        <div id="existingComments">  
            <span><?php echo __('Loading') . '...';?></span>
        </div>
        <?php if ($commentPermissions->canCreate()):?>
        <br class="clear" />
        <br class="clear" />
        <textarea name="leaveComment" id="leaveComment" cols="40" rows="4" class="commentTextArea"></textarea>
        <span id="commentError"></span>
        <?php endif;?>
    </form>        
    </p>
  </div>
  <div class="modal-footer">
    <?php if ($commentPermissions->canCreate()):?>
    <input type="button" class="btn" id="commentSave" value="<?php echo __('Save'); ?>" />
    <?php endif;?>
    <input type="button" class="btn reset" data-dismiss="modal" id="commentCancel" value="<?php echo __('Cancel'); ?>" />
  </div>
</div>
<!-- end of comment dialog-->
<?php }?>

<script type="text/javascript">
    //<![CDATA[

    var leaveRequestId = <?php echo $leaveRequestId; ?>;
    var leave_status_pending = 'Pending Approval'; // TO DO: Fix, check if compatible with localization
    var ess_mode = '<?php echo ($essMode) ? '1' : '0'; ?>';
    var lang_Required = '<?php echo __(ValidationMessages::REQUIRED);?>';
    var lang_comment_successfully_saved = '<?php echo __(TopLevelMessages::SAVE_SUCCESS); ?>';
    var lang_comment_save_failed = '<?php echo __(TopLevelMessages::SAVE_FAILURE); ?>'; 
    var lang_Processing = '<?php echo __('Processing'); ?>...';
    var lang_Close = '<?php echo __('Close');?>';
    var lang_Date = '<?php echo __('Date');?>';
    var lang_Time = '<?php echo __('Time');?>';
    var lang_Author = '<?php echo __('Author');?>';
    var lang_Comment = '<?php echo __('Comment');?>';
    var lang_Loading = '<?php echo __('Loading');?>...';
    var lang_LengthExceeded = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 255)); ?>';
    var lang_LeaveComments = '<?php echo __('Leave Comments'); ?>';
    var lang_LeaveRequestComments = '<?php echo __('Leave Request Comments'); ?>';
    var lang_selectAction = '<?php echo __("Select Action");?>';
    var lang_Close = '<?php echo __('Close');?>';    
    var getCommentsUrl = '<?php echo url_for('leave/getLeaveCommentsAjax'); ?>';
    var commentUpdateUrl = '<?php echo public_path('index.php/leave/updateComment'); ?>';
    var backUrl = '<?php echo url_for($backUrl); ?>';     
    //]]>
</script>


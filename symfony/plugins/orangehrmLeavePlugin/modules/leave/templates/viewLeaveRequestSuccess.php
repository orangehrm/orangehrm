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
    var lang_Required = '<?php echo __js(ValidationMessages::REQUIRED);?>';
    var lang_comment_successfully_saved = '<?php echo __js(TopLevelMessages::SAVE_SUCCESS); ?>';
    var lang_comment_save_failed = '<?php echo __js(TopLevelMessages::SAVE_FAILURE); ?>';
    var lang_Processing = '<?php echo __js('Processing'); ?>...';
    var lang_Close = '<?php echo __js('Close');?>';
    var lang_Date = '<?php echo __js('Date');?>';
    var lang_Time = '<?php echo __js('Time');?>';
    var lang_Author = '<?php echo __js('Author');?>';
    var lang_Comment = '<?php echo __js('Comment');?>';
    var lang_Loading = '<?php echo __js('Loading');?>...';
    var lang_LengthExceeded = '<?php echo __js(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 255)); ?>';
    var lang_LeaveComments = '<?php echo __js('Leave Comments'); ?>';
    var lang_LeaveRequestComments = '<?php echo __js('Leave Request Comments'); ?>';
    var lang_selectAction = '<?php echo __js("Select Action");?>';
    var lang_Close = '<?php echo __js('Close');?>';
    var getCommentsUrl = '<?php echo url_for('leave/getLeaveCommentsAjax'); ?>';
    var commentUpdateUrl = '<?php echo public_path('index.php/leave/updateComment'); ?>';
    var backUrl = '<?php echo url_for($backUrl); ?>';     
    //]]>
</script>


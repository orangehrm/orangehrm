<?php

$statusFilters = $form->getStatusFilters();

$messageType = empty($messageType) ? '' : "messageBalloon_{$messageType}";
$leaveData = $form->getList();
$searchActionButtons = $form->getSearchActionButtons();

?>

<?php $isDefaultPageView = !empty($isDefaultPage) ? $isDefaultPage : 0; ?>

<?php echo stylesheet_tag('../orangehrmCoreLeavePlugin/css/viewLeaveListSuccess'); ?>
<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css')?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo public_path('../../themes/orange/css/jquery/jquery.autocomplete.css')?>" rel="stylesheet" type="text/css"/>

<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.draggable.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.resizable.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.dialog.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.autocomplete.js')?>"></script>
<?php echo javascript_include_tag('orangehrm.datepicker.js')?>

<?php if($messageType == "messageBalloon_notice") {?>
<div class="<?php echo $messageType; ?>"><?php echo $message; ?></div>
<?php }?>
<div class="outerbox">
	<div class="mainHeading"><h2><?php echo __($form->getTitle()); ?></h2></div>
	<?php if (!$form->isDetailed()) { ?>
	<div class="formWrapper">
		<form id="frmFilterLeave" name="frmFilterLeave" method="post" action="<?php echo url_for($baseUrl); ?>">
            <label class="mainLabel"><?php echo __("From");?></label>
            <?php echo $form['_csrf_token']; ?>
			<?php echo $form['calFromDate']->render(); ?>
            <br class="clear" />
			<label class="mainLabel"><?php echo __("To");?></label>
			<?php echo $form['calToDate']->render(); ?>
			<br class="clear" />

			<label class="mainLabel"><?php echo __("Show Leave with Status");?></label>
			<?php foreach ($statusFilters as $filter) { ?>
				<?php echo $filter->render($filter->getName()) ?>
			<?php } ?>
			<?php if (isset($form['txtEmployee'])) { ?>
                <br class="clear" />
				<?php echo $form['txtEmployee']->renderLabel(__("Employee"), array('class' => 'mainLabel')); ?>
				<?php echo $form['txtEmployee']->render(); ?>
                <?php echo $form['txtEmpID']->render();?>
			<?php } ?>
			<?php if (isset($form['cmbSubunit'])) { ?>
				<br class="clear" />
				<?php echo $form['cmbSubunit']->renderLabel(__("Sub Unit"), array('class' => 'mainLabel')); ?>
				<?php echo $form['cmbSubunit']->render(); ?>
			<?php } ?>
            <?php if (isset($form['cmbWithTerminated'])) { ?>
				<br class="clear" />
				<label class="mainLabel"><?php echo __('With Terminated Employees'); ?></label><?php echo $form['cmbWithTerminated']->render(); ?>
			<?php } ?>
            <br class="clear" />
                        <div class="buttonWrapper">
			<?php foreach ($searchActionButtons as $id => $button) {
				echo $button->render($id), "\n";
				}
			?>
                        <input type="hidden" name="pageNo" id="pageNo" value="<?php echo $form->pageNo; ?>" />
                        <input type="hidden" name="hdnAction" id="hdnAction" value="search" />
        </div>
		</form>
	</div>
	<?php } ?>
<input type="hidden" name="pageNoDet" id="pageNoDet" value="<?php echo isset($page)?$page:''; ?>" />
</div> <!-- End of outerbox -->
<?php if($messageType == "messageBalloon_success") {?>
<div class="<?php echo $messageType; ?>"><?php echo $message; ?></div>
<?php }?>

<!--this is ajax message place -->
<div id="msgPlace"></div>
<!-- end of ajax message place -->

<?php include_component('core', 'ohrmList'); ?>
<input type="hidden" name="hdnMode" value="<?php echo $mode; ?>" />
<!-- comment dialog -->

<div id="commentDialog" title="<?php echo __('Leave Comment'); ?>">
    <form action="updateComment" method="post" id="frmCommentSave">
        <input type="hidden" id="leaveId" />
        <input type="hidden" id="leaveOrRequest" />
        <textarea name="leaveComment" id="leaveComment" cols="40" rows="10" class="commentTextArea"></textarea>
        <br class="clear" />
        <div class="error" id="commentError"></div>
        <div><input type="button" id="commentSave" class="plainbtn" value="<?php echo __('Edit'); ?>" />
            <input type="button" id="commentCancel" class="plainbtn" value="<?php echo __('Cancel'); ?>" /></div>
    </form>
</div>

<!-- end of comment dialog-->

<input type="hidden" id="overQuotaSelectId" value="" />
<input type="hidden" id="leaveRequestHiddenId" value="" />

<script type="text/javascript">
//<![CDATA[

    function handleSaveButton() {
        $(this).attr('disabled', true);
        $('select[name^="select_leave_action_"]').each(function() {
            var id = $(this).attr('id').replace('select_leave_action_', '');
            if ($(this).val() == '') {
                $('#hdnLeaveRequest_' + id).attr('disabled', true);
            } else {
                $('#hdnLeaveRequest_' + id).val($(this).val());
            }

            if ($(this).val() == '') {
                $('#hdnLeave_' + id).attr('disabled', true);
            } else {
                $('#hdnLeave_' + id).val($(this).val());
            }
        });

        var url = $('#frmList_ohrmListComponent').attr('action');

        /* Suppose if it is the detailed screen */
        <?php if(isset($leaveRequestId) && trim($leaveRequestId) != '') {?>
            url = url + "/id/" + <?php echo $leaveRequestId;?>;
            $('#frmList_ohrmListComponent').attr('action', url);
        <?php } ?>

        <?php if(isset($mode) && trim($mode) != '') {?>
            url = url + "/hdnMode/<?php echo $mode;?>";
            $('#frmList_ohrmListComponent').attr('action', url);
        <?php } ?>
        $('#frmList_ohrmListComponent').submit();
    }

    function handleBackButton() {

        var url = "../../../leave/viewLeaveList/pageNo/" + document.getElementById('pageNoDet').value;

        <?php if (isset($mode) && $mode == LeaveListForm::MODE_MY_LEAVE_DETAILED_LIST) {?>
                url = "../viewMyLeaveList";
        <?php }?>
        window.location = url;
    }

    var mode = '<?php echo ($form->isDetailed()) ? 'detailed' : 'compact'; ?>';
    var quota = new Array();
    <?php
        if (isset($quotaArray)) {
            foreach ($quotaArray as $key => $val) {
                echo "quota[\"".$key."\"] = {$val};\n ";
            }
        }
    ?>

        <?php if($form->isDetailed()): ?>
        var mode = 'detailed';
        <?php else: ?>
        var mode = 'compact';
        <?php endif; ?>

        var quota = new Array();

        <?php

            if (isset($quotaArray)) {

                foreach ($quotaArray as $key => $val) {

                    echo "quota[\"".$key."\"] = $val;\n ";

                }

            }

        ?>

        function setPage() {

            if (document.frmFilterLeave.pageNo.value) {
                document.getElementById('frmList_ohrmListComponent').action = document.getElementById('frmList_ohrmListComponent').action + '/currentPage/' + document.frmFilterLeave.pageNo.value;
            }
        }

        function submitPage(pageNo) {

            document.frmFilterLeave.pageNo.value = pageNo;
            document.frmFilterLeave.hdnAction.value = 'paging';
            document.getElementById('frmFilterLeave').submit();

        }

	$(document).ready(function(){
        
        var lang_typeHint = "<?php echo __("Type for hints");?>" + "...";
        var isInitialPage = "<?php echo $isDefaultPageView; ?>";
        
        var isMyLeaveListDefaultView = <?php echo $isMyLeaveListDefaultView ? 'true' : 'false'; ?>;
        
        if(isInitialPage == 1){
            $('#chkSearchFilter_1').attr('checked', 'checked');
        }
        
        if(isMyLeaveListDefaultView) {
            $('.checkbox').attr('checked', 'checked');
        }
        
        if ($("#txtEmployee").val() == '' || $("#txtEmployee").val() == lang_typeHint) {
            $("#txtEmployee").addClass("inputFormatHint").val(lang_typeHint);
        }
        
        $("#txtEmployee").one('focus', function() {

            if ($(this).hasClass("inputFormatHint")) {
                $(this).val("");
                $(this).removeClass("inputFormatHint");
            }
       });
       
        var data	= <?php echo str_replace('&#039;',"'",$form->getEmployeeListAsJson());?>
        //Auto complete
        $("#txtEmployee").autocomplete(data, {
            formatItem: function(item) {
                return item.name;
            }
            ,matchContains:true
            }).result(function(event, item) {
                $('#txtEmpID').val(item.id);
            }
        );

        $("#txtEmployee").change(function(){
            autoFill('txtEmployee', 'txtEmpID', data);
        });

        function autoFill(selector, filler, data) {
            $("#" + filler).val(0);
            if($("#" + selector).val().trim() == "") {
                $("#" + filler).val("");
            }
            
            $.each(data, function(index, item){
                if(item.name.toLowerCase() == $("#" + selector).val().toLowerCase()) {
                    $("#" + filler).val(item.id);
                    return true;
                }
            });
        }

         //dissabling dialog by default
            $("#commentDialog").dialog({
                autoOpen: false,
                width: 350,
                height: 300
            });
        
            daymarker.bindElement('.ohrm_datepicker', {
                onSelect: function(date){},
                dateFormat : 'yy-mm-dd'
            });

            $('.ohrm_datepicker').each(function() {
                $(this).next().click(function(){
	            daymarker.show('#' + $(this).prev().attr('id'));
	        });
            });

            $('input#checkAll').click(function() {
                $(this).siblings('.checkbox').attr('checked', $(this).attr('checked'));
            });

            $('.checkbox').each(function() {
                if ($(this).attr('id') != 'checkAll') {
                    $(this).click(function() {
                        var allChecked = true;
                        $(this).siblings('.checkbox').each(function() {
                            if ($(this).attr('id') != 'checkAll') {
                                    allChecked = (allChecked && $(this).attr('checked'));
                            }
                        });
                        allChecked = (allChecked && $(this).attr('checked'));
                        $('input#checkAll').attr('checked', allChecked);
                    });
                }
            });

            //open when the pencil mark got clicked
            $('.dialogInvoker').click(function() {
                $("#leaveComment").attr("disabled","disabled");
                //removing errors message in the comment box
                $("#commentError").html("");
                
                $("#commentSave").attr("value", "<?php echo __('Edit'); ?>");

                /* Extracting the request id */
                var id = $(this).parent().siblings('input[id^="hdnLeaveRequest_"]').val();
                if (!id) {
                    var id = $(this).parent().siblings('input[id^="hdnLeave_"]').val();
                }
                var comment = $('#hdnLeaveComment-' + id).val();
                var typeOfView = (mode == 'compact') ? 'request' : 'leave';

                $('#leaveId').val(id);
                $('#leaveComment').val(comment);
                $('#leaveOrRequest').val(typeOfView);

                $('#commentDialog').dialog('open');
            });

            //closes the dialog
            $("#commentCancel").click(function() {
                $("#commentDialog").dialog('close');
            });

            //on clicking on save button
            $("#commentSave").click(function() {
                if($("#commentSave").attr("value") == "<?php echo __('Edit'); ?>") {
                    $("#leaveComment").removeAttr("disabled");
                    $("#commentSave").attr("value", "<?php echo __('Save'); ?>");
                    return;
                }

                if($('#commentSave').attr('value') == "<?php echo __('Save'); ?>") {
                    $('#commentError').html('');
                    var comment = $('#leaveComment').val().trim();
                    if(comment.length > 250) {
                        $('#commentError').html('<?php echo __('Comment length should be less than 250 characters'); ?>');
                        return;
                    }

                    /* Setting the comment in the label */
                    var commentLabel = trimComment(comment);

                    /* If there is no-change between original and updated comments then don't show success message */
                    if($('#hdnLeaveComment-' + $("#leaveId").val()).val().trim() == comment) {
                        $('#commentDialog').dialog('close');
                        return;
                    }

                    /* We set updated comment for the hidden comment field */
                    $('#hdnLeaveComment-' + $('#leaveId').val()).val(comment);

                    /* Posting the comment */
                    var url = '<?php echo public_path('index.php/leave/updateComment'); ?>';
                    var data = 'leaveRequestId=' + $('#leaveId').val() + '&leaveComment=' + encodeURIComponent(comment);

                    /* This is specially for detailed view */
                    if($('#leaveOrRequest').val() == 'leave') {
                        data = 'leaveId=' + $('#leaveId').val() + '&leaveComment=' + encodeURIComponent(comment);
                    }

                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: data,
                        success: function(flag) {
                            $('#msgPlace').removeAttr('class');
                            $('.messageBalloon_success').remove();
                            $('#msgPlace').html('');
                            if(flag == 1) {
                                var id = $('#leaveId').val();
                                $('#commentContainer-' + id).html(commentLabel);
                                $('#hdnLeaveComment-' + id).val(comment);
                                $('#msgPlace').attr('class', 'messageBalloon_success');
                                $('#msgPlace').html('<?php echo __('Comment Successfully Saved'); ?>');
                            }
                        }
                    });

                    $("#commentDialog").dialog('close');
                    return;
                }
            });

            $('#btnSearch').click(function() {
                $('#frmFilterLeave').attr('action', $('#frmFilterLeave').attr('action') + '/pageNo/1');
                $('#frmFilterLeave').submit();
            });


            $('#btnReset').click(function() {
                $('<input/>').attr('type', 'hidden')
                .attr('name', 'reset')
                .attr('value', '1')
                .appendTo('#frmFilterLeave');
                
                $('#frmFilterLeave').submit();
            });

//            $('#btnBack').click(function() {
//                var url = "../../../leave/viewLeaveList";
//
//                <?php if (isset($mode) && $mode == LeaveListForm::MODE_MY_LEAVE_DETAILED_LIST) {?>
//                        url = "../viewMyLeaveList";
//                <?php }?>
//                window.location = url;
//            });

//            $('#btnSave').click(function() {alert('hellooo');
//                $(this).attr('disabled', true);
//                $('td.actions input:hidden').each(function() {
//                    if ($(this).val() == '') {
//                        $(this).attr('disabled', true);
//                    }
//                });
//
//                //suppose if it is the detailed screen
//                <?php if(isset($leaveRequestId) && trim($leaveRequestId) != "") {?>
//                    var url = $('#frmSaveLeave').attr('action') + "/id/" + <?php echo $leaveRequestId;?>;
//                    $('#frmSaveLeave').attr('action', url);
//                <?php } ?>
//                $('#frmSaveLeave').submit();
//            });


            $('select.select_action').bind("change",function() {

                var requestId = $(this).attr('name').substring(20);

                if (mode == 'detailed') {
                    $('#leave-'+requestId).val($(this).val());
                } else {
                    $('#leaveRequest-'+requestId).val($(this).val());
                }

            });


        $('.data-table tbody tr.r1').hover(function() {
            $(this).removeClass();
            $(this).attr('class', 'r1 highlightRow');
        });

        $('.data-table tbody tr.r1').mouseout(function() {
           var i=0;
           $('.data-table tbody tr.r1').each(function(index, item) {
                $(item).attr('class', "r1 even");
                if(i % 2 == 0) {
                    $(item).attr('class', "r1 odd");
                }
                i = i + 1;
           });
            
        });

    });
    
    function trimComment(comment) {
        if (comment.length > 35) {
            comment = comment.substr(0, 35) + '...';
        }
        return comment;
    }
//]]>
</script>


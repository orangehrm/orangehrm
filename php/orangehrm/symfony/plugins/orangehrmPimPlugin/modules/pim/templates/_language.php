<?php  
if (($section == 'language') && isset($message) && isset($messageType)) {
    $tmpMsgClass = "messageBalloon_{$messageType}";
    $tmpMsg = $message;
} else {
    $tmpMsgClass = '';
    $tmpMsg = '';
}
?>
<div id="languageMessagebar" class="<?php echo $tmpMsgClass; ?>">
    <span style="font-weight: bold;"><?php echo $tmpMsg; ?></span>
</div>                                 


<div class="sectionDiv" id="sectionLanguage">
    <div><h3><?php echo __('Languages'); ?></h3></div>

    <div class="outerbox" id="changeLanguage" style="width:500px;">
        <div class="mainHeading"><h4 id="headChangeLanguage"><?php echo __('Add Language'); ?></h4></div>
        <form id="frmLanguage" action="<?php echo url_for('pim/saveDeleteLanguage?empNumber=' . $empNumber . "&option=save"); ?>" method="post">

            <?php echo $form['_csrf_token']; ?>
            <?php echo $form['emp_number']->render(); ?>

            <?php echo $form['code']->renderLabel(__('Language') . ' <span class="required">*</span>'); ?>
            <?php echo $form['code']->render(array("class" => "formSelect")); ?>
            <span id="static_language_code" style="display:none;"></span>            
            <br class="clear"/>

            <?php echo $form['lang_type']->renderLabel(__('Fluency') . ' <span class="required">*</span>'); ?>
            <?php echo $form['lang_type']->render(array("class" => "formSelect")); ?>
            <span id="static_lang_type" style="display:none;"></span>            
            <br class="clear"/>

            <?php echo $form['competency']->renderLabel(__('Competency') . ' <span class="required">*</span>'); ?>
            <?php echo $form['competency']->render(array("class" => "formSelect")); ?>
            <br class="clear"/>
            
            <?php echo $form['comments']->renderLabel(__('Comments')); ?>
            <?php echo $form['comments']->render(array("class" => "formInputText")); ?>
            <br class="clear"/>

            <div class="formbuttons">
                <input type="button" class="savebutton" id="btnLanguageSave" value="<?php echo __("Save"); ?>" />
                <input type="button" class="savebutton" id="btnLanguageCancel" value="<?php echo __("Cancel"); ?>" />
            </div>
        </form>
    </div>
    <div class="smallText" id="languageRequiredNote"><?php echo __('Fields marked with an asterisk')?>
        <span class="required">*</span> <?php echo __('are required.')?></div>
    <br />
    <div id="actionLanguage">
        <input type="button" value="<?php echo __("Add");?>" class="savebutton" id="addLanguage" />&nbsp;
        <input type="button" value="<?php echo __("Delete");?>" class="savebutton" id="delLanguage" />
    </div>

    <form id="frmDelLanguage" action="<?php echo url_for('pim/saveDeleteLanguage?empNumber=' . $empNumber . "&option=delete"); ?>" method="post">
        <div class="outerbox" id="tblLanguage">
            <table width="100%" cellspacing="0" cellpadding="0" class="data-table" border="0">
                <thead>
                <tr>
                    <td class="check"><input type="checkbox" id="languageCheckAll" /></td>
                    <td><?php echo __('Language');?></td>
                    <td><?php echo __('Fluency');?></td>
                    <td><?php echo __('Competency');?></td>                    
                    <td><?php echo __('Comments');?></td>
                </tr>
                </thead>
                <tbody>
                    <?php
                    $languages = $form->empLanguageList;//var_dump($languages->toArray());die;
                    $row = 0;

                    foreach ($languages as $language) {                        
                        $cssClass = ($row % 2) ? 'even' : 'odd';
                        $languageName = $language->getLanguage()->getLangName();
                        ?>
                    <tr class="<?php echo $cssClass;?>">
                <td class="check"><input type="hidden" class="code" value="<?php echo htmlspecialchars($language->code); ?>" />
                <input type="hidden" class="language_name" value="<?php echo htmlspecialchars($languageName); ?>" />
                <input type="hidden" class="lang_type" value="<?php echo htmlspecialchars($language->lang_type); ?>" />
                <input type="hidden" class="competency" value="<?php echo htmlspecialchars($language->competency); ?>" />

                <input type="checkbox" class="chkbox" value="<?php echo $language->code . "_" . $language->lang_type;?>" name="delLanguage[]"/></td>
                <td><a href="#" class="edit"><?php echo htmlspecialchars($languageName);?></a></td>
                <td><?php echo htmlspecialchars($form->getLangTypeDesc($language->lang_type));?></td>
                <td><?php echo htmlspecialchars($form->getCompetencyDesc($language->competency));?></td>
                <td class="comments"><?php echo htmlspecialchars($language->comments);?></td>
                </tr>
                    <?php $row++;
                }?>
                </tbody>
            </table>
        </div>
    </form>

</div>
<script type="text/javascript">
    //<![CDATA[

    var fileModified = 0;
    var lang_addLanguage = "<?php echo __('Add Language');?>";
    var lang_editLanguage = "<?php echo __('Edit Language');?>";
    var lang_languageRequired = "<?php echo __("Language is required");?>";
    var lang_languageTypeRequired = "<?php echo __("Fluency is required");?>";
    var lang_competencyRequired = "<?php echo __("Competency is required");?>";
    var lang_selectLanguageToDelete = "<?php echo __('Please Select At Least One Language Item To Delete');?>";
    var lang_commentsMaxLength = "<?php echo __('Comment length cannot exceed 100 characters');?>";
    //]]>
</script>

<script type="text/javascript">
//<![CDATA[

$(document).ready(function() {

    //hide add section
    $("#changeLanguage").hide();
    $("#languageRequiredNote").hide();

    //hiding the data table if records are not available
    if($("div#tblLanguage table.data-table .chkbox").length == 0) {
        $("#tblLanguage").hide();
        $("#editLanguage").hide();
        $("#delLanguage").hide();
    }

    //if check all button clicked
    $("#languageCheckAll").click(function() {
        $("div#tblLanguage .chkbox").removeAttr("checked");
        if($("#languageCheckAll").attr("checked")) {
            $("div#tblLanguage .chkbox").attr("checked", "checked");
        }
    });

    //remove tick from the all button if any checkbox unchecked
    $("div#tblLanguage .chkbox").click(function() {
        $("#languageCheckAll").removeAttr('checked');
        if($("div#tblLanguage .chkbox").length == $("div#tblLanguage .chkbox:checked").length) {
            $("#languageCheckAll").attr('checked', 'checked');
        }
    });

    $("#addLanguage").click(function() {

        clearMessageBar();
        $('div#changeLanguage label.error').hide();        
        

        //changing the headings
        $("#headChangeLanguage").text(lang_addLanguage);
        $("div#tblLanguage .chkbox").hide();
        $("#languageCheckAll").hide();

        //hiding action button section
        $("#actionLanguage").hide();

        $('#static_language_code').hide().val("");        
        $('#static_lang_type').hide().val("");
        $("#language_code").show().val("");
        $("#language_lang_type").show().val("");
        $("#language_comptency").val("");

        //show add form
        $("#changeLanguage").show();
        $("#languageRequiredNote").show();
    });

    //clicking of delete button
    $("#delLanguage").click(function(){

        clearMessageBar();

        if ($("div#tblLanguage .chkbox:checked").length > 0) {
            $("#frmDelLanguage").submit();
        } else {
            $("#languageMessagebar").attr('class', 'messageBalloon_notice').text(lang_selectLanguageToDelete);
        }

    });

    $("#btnLanguageSave").click(function() {
        clearMessageBar();

        $("#frmLanguage").submit();
    });

    //form validation
    var languageValidator =
        $("#frmLanguage").validate({
        rules: {
            'language[code]': {required: true},
            'language[lang_type]': {required: true},
            'language[competency]': {required: true},
            'language[comments]' : {required: false, maxlength:100}
        },
        messages: {
            'language[code]': {required: lang_languageRequired},
            'language[lang_type]': {required: lang_languageTypeRequired},
            'language[competency]': {required: lang_competencyRequired},
            'language[comments]' : {maxlength: lang_commentsMaxLength}
        },

        errorElement : 'div',
        errorPlacement: function(error, element) {
            error.insertAfter(element.next(".clear"));
            error.insertAfter(element.next().next(".clear"));

        }
    });

    $("#btnLanguageCancel").click(function() {
        clearMessageBar();

        languageValidator.resetForm();
        
        $('div#changeLanguage label.error').hide();

        $("div#tblLanguage .chkbox").removeAttr("checked").show();
        
        //hiding action button section
        $("#actionLanguage").show();
        $("#changeLanguage").hide();
        $("#languageRequiredNote").hide();        
        $("#languageCheckAll").show();
        $('#static_language_code').hide().val("");
        $('#static_lang_type').hide().val("");
    });
    
    $('form#frmDelLanguage a.edit').click(function() {
        clearMessageBar();

        //changing the headings
        $("#headChangeLanguage").text(lang_editLanguage);

        languageValidator.resetForm();

        $('div#changeLanguage label.error').hide();

        //hiding action button section
        $("#actionLanguage").hide();

        //show add form
        $("#changeLanguage").show();
        var parentRow = $(this).closest("tr");
                                
        var code = parentRow.find('input.code:first').val();

        var langType = parentRow.find('input.lang_type').val();
        var comments = $(this).closest("tr").find('td:last').html();
        
        $('#language_code').hide().val(code);
        $("#language_lang_type").hide().val(langType);
        var langTypeText = $("#language_lang_type option:selected").text();
        
        $('#static_language_code').text(parentRow.find('input.language_name').val()).show();
        $('#static_lang_type').text(langTypeText).show();
        
        $("#language_competency").val(parentRow.find('input.competency').val());
        $('#language_comments').val(comments);

        $("#languageRequiredNote").show();

        $("div#tblLanguage .chkbox").hide();
        $("#languageCheckAll").hide();        
    });
});

//]]>
</script>
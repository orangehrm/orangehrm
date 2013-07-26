<?php  
$haveLanguage = count($form->empLanguageList) > 0;
?>                         

<a name="language"></a>
<?php if ($languagePermissions->canCreate() || ($haveLanguage && $languagePermissions->canUpdate())) { ?>
    <div id="changeLanguage">
        <div class="head">
            <h1 id="headChangeLanguage"><?php echo __('Add Language'); ?></h1>
        </div>
            
        <div class="inner">
            <form id="frmLanguage" action="<?php echo url_for('pim/saveDeleteLanguage?empNumber=' . 
                    $empNumber . "&option=save"); ?>" method="post">
                <fieldset>
                    <ol>
                        <?php echo $form->render(); ?>
                        
                        <li class="required">
                            <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                        </li>
                    </ol>
                    <p>
                        <input type="button" class="" id="btnLanguageSave" value="<?php echo __("Save"); ?>" />
                        <input type="button" class="reset" id="btnLanguageCancel" value="<?php echo __("Cancel"); ?>" />
                    </p>
                </fieldset>
            </form>
        </div>
    </div> <!-- changeLanguage -->
<?php } ?>

<div class="miniList" id="tblLanguage">
    <div class="head">
        <h1><?php echo __("Languages"); ?></h1>
    </div>

    <div class="inner">

        <?php if ($languagePermissions->canRead()) : ?>

            <?php include_partial('global/flash_messages', array('prefix' => 'language')); ?>

            <form id="frmDelLanguage" action="<?php echo url_for('pim/saveDeleteLanguage?empNumber=' . 
                    $empNumber . "&option=delete"); ?>" method="post">
                <?php echo $listForm ?>
                <p id="actionLanguage">
                    <?php if ($languagePermissions->canCreate()) { ?>
                    <input type="button" value="<?php echo __("Add"); ?>" class="" id="addLanguage" />&nbsp;
                    <?php } ?>
                    <?php if ($languagePermissions->canDelete()) { ?>
                    <input type="button" value="<?php echo __("Delete"); ?>" class="delete" id="delLanguage" />
                    <?php } ?>
                </p>
                <table id="lang_data_table" cellpadding="0" cellspacing="0" width="100%" class="table tablesorter">
                    <thead>
                        <tr>
                            <?php if ($languagePermissions->canDelete()) { ?>
                            <th class="check" width="2%"><input type="checkbox" id="languageCheckAll" /></th>
                            <?php } ?>
                            <th><?php echo __('Language'); ?></th>
                            <th><?php echo __('Fluency'); ?></th>
                            <th><?php echo __('Competency'); ?></th>                    
                            <th><?php echo __('Comments'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$haveLanguage) { ?>
                            <tr>
                                <?php if ($languagePermissions->canDelete()) { ?>
                                    <td class="check"></td>
                                <?php } ?>
                                <td><?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        <?php } else { ?>                        
                            <?php
                            $languages = $form->empLanguageList; //var_dump($languages->toArray());die;
                            $row = 0;
                            foreach ($languages as $language) :
                                $cssClass = ($row % 2) ? 'even' : 'odd';
                                $languageName = $language->getLanguage()->getName();
                                ?>
                                <tr class="<?php echo $cssClass; ?>">
                                    <td class="check">
                                        <input type="hidden" class="language_name" 
                                               value="<?php echo htmlspecialchars($languageName); ?>" />
                                        <input type="hidden" class="lang_type" 
                                               value="<?php echo htmlspecialchars($language->fluency); ?>" />
                                        <input type="hidden" class="competency" 
                                               value="<?php echo htmlspecialchars($language->competency); ?>" />
                                        <input type="hidden" class="code" 
                                               value="<?php echo htmlspecialchars($language->langId); ?>" />
                                        <?php if ($languagePermissions->canDelete()) { ?>
                                        <input type="checkbox" class="chkbox" 
                                               value="<?php echo $language->langId . "_" . $language->fluency; ?>" 
                                               name="delLanguage[]"/>
                                        <?php } else { ?>
                                        <input type="hidden" class="chkbox" 
                                               value="<?php echo $language->langId . "_" . $language->fluency; ?>" 
                                               name="delLanguage[]"/>
                                        <?php } ?>
                                    </td>
                                    <td class="name">
                                        <?php if ($languagePermissions->canUpdate()) { ?>
                                        <a href="#" class="edit"><?php echo htmlspecialchars($languageName); ?></a>
                                        <?php } else {
                                            echo htmlspecialchars($languageName);
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($form->getLangTypeDesc($language->fluency)); ?></td>
                                    <td><?php echo htmlspecialchars($form->getCompetencyDesc($language->competency)); ?></td>
                                    <td class="comments"><?php echo htmlspecialchars($language->comments); ?></td>
                                </tr>
                                <?php
                                $row++;
                            endforeach;
                        } ?>
                    </tbody>
                </table>
            </form>

        <?php else : ?>
            <div><?php echo __(CommonMessages::RESTRICTED_SECTION); ?></div>
        <?php endif; ?>

    </div>
</div> <!-- miniList-tblLanguage sectionLanguage -->

<script type="text/javascript">
    //<![CDATA[

    var fileModified = 0;
    var lang_addLanguage = "<?php echo __('Add Language');?>";
    var lang_editLanguage = "<?php echo __('Edit Language');?>";
    var lang_languageRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_languageTypeRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_competencyRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_selectLanguageToDelete = "<?php echo __(TopLevelMessages::SELECT_RECORDS);?>";
    var lang_commentsMaxLength = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100));?>";
    //]]>
</script>

<script type="text/javascript">
//<![CDATA[

$(document).ready(function() {
    
    $('#language_code').after('<span id="static_language_code" style="display:none;"></span>');
    $('#language_lang_type').after('<span id="static_lang_type" style="display:none;"></span>');
    
    function addEditLinks() {
        // called here to avoid double adding links - When in edit mode and cancel is pressed.
        removeEditLinks();
        $('form#frmDelLanguage table tbody td.name').wrapInner('<a class="edit" href="#"/>');
    }

    function removeEditLinks() {
        $('form#frmDelLanguage table tbody td.name a').each(function(index) {
            $(this).parent().text($(this).text());
        });
    }
    
    //hide add section
    $("#changeLanguage").hide();
    $("#languageRequiredNote").hide();

    //hiding the data table if records are not available
    if($("div#tblLanguage .chkbox").length == 0) {
        //$("#tblLanguage").hide();
        $('div#tblLanguage .check').hide();
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
    
    //hide already added languages and fluencys
    $("#language_code").change(function() {
        //show all the options to reseting hide options
        $("#language_lang_type option").each(function() {
            $(this).show();
        });
        $("#language_lang_type").val("");
        var $table_tr = $("#lang_data_table tr");
        var i=0;
        //hide already added optons for selected language
        $table_tr.each(function() {
            i++;
            if (i != 1) {           // skip heading tr
                if ($('#language_code').val() == $(this).find('td:eq(0)').find('input[class="code"]').val()){
                    $td = $(this).find('td:eq(0)').find('input[class="lang_type"]').val();
                    $("#language_lang_type option[value=" + $td + "]").hide();
                }
            }
        });        
    });
    
    $("#addLanguage").click(function() {
        removeEditLinks();
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
        
        //show all the options to reseting hide options
        $("#language_lang_type option").each(function() {
            $(this).show();
        });
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
        }
    });

    $("#btnLanguageCancel").click(function() {
        clearMessageBar();
        <?php if ($languagePermissions->canUpdate()){?>
            addEditLinks();
        <?php }?>
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
        
        //remove if disabled while edit
        $('#language_code').removeAttr('disabled');
        $('#language_lang_type').removeAttr('disabled');
    });
    
    $('form#frmDelLanguage a.edit').live('click', function(event) {
        event.preventDefault();
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
        
        $('#language_code').val(code).hide();
        $("#language_lang_type").val(langType).hide();
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
$(document).ready(function() {

    disableWidgets()
   
    $('#btnSave').click(function() {
        //if user clicks on Edit make all fields editable
        if($("#btnSave").attr('value') == lang_edit) {
            enableWidgets()
            $("#btnSave").attr('value', lang_save)
        }
        else {
            $("#btnSave").attr('value', lang_edit)
            $('#frmLocalization').submit()
        }
    });
    
});

function disableWidgets(){
    $('#localization_dafault_language').attr('disabled', 'disabled')
    $('#localization_use_browser_language').attr('disabled', true)
    $('#localization_default_date_format').attr('disabled', 'disabled')
}

function enableWidgets(){
    $('#localization_dafault_language').removeAttr('disabled')
    $('#localization_use_browser_language').removeAttr('disabled')
    $('#localization_default_date_format').removeAttr('disabled')
}
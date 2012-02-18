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

    $("#languageDialog").dialog({
        autoOpen: false,
        modal: true,
        width: 340,
        height:170,
        position: 'middle'
    });

    $("#dialogOk").click(function(){
        $("#languageDialog").dialog("close");
    });
    
   // For reloading main menu (index.php)
   if (reloadParent) {
       window.setTimeout(function() {
       parent.location.reload();
           }, 500);
   }    

    
});

function openDialogue(){
    $("#languageDialog").dialog("open")
}


function disableWidgets(){
    $('#localization_dafault_language').attr('disabled', 'disabled')
    $('.formSelect').attr('disabled', 'disabled')
    $('#localization_default_date_format').attr('disabled', 'disabled')
}

function enableWidgets(){
    $('#localization_dafault_language').removeAttr('disabled')
    $('.formSelect').removeAttr('disabled')
    $('#localization_default_date_format').removeAttr('disabled')
}
$(document).ready(function() {
    
    executeLoadtimeActions();
    loadSaveButtonBehavior();
    
});


function executeLoadtimeActions() {
    
   $('.checkbox').each(function(){
       $(this).attr('disabled', 'disabled');
   });
   
}

function loadSaveButtonBehavior() {
    
    $('#btnSave').click(function() {
        
        var buttonName = $(this).val();
        
        if (buttonName == lang_edit) {
            
            $(this).val(lang_save);
            $(this).attr('title', lang_save);
            
           $('.checkbox').each(function(){
               $(this).removeAttr('disabled');
           });
           
           $('#moduleConfig_admin').attr('disabled', 'disabled');
           $('#moduleConfig_pim').attr('disabled', 'disabled');
           $('#moduleConfig_help').attr('disabled', 'disabled');
            
        } else if (buttonName == lang_save) {
            $('#frmSave').submit();
        }

    });
    
}

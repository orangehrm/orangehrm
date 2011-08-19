$(document).ready(function() {
    $('#cancelBtn').click(function(){
        window.location.replace(cancelBtnUrl+'?id='+candidateId);
    });
    $('#actionBtn').click(function(){
        $('#frmCandidateVacancyStatus').submit();
    });
    
    	$('#btnSave').click(function() {
            if($("#btnSave").attr('value') == lang_edit) {
                $(".formInputText").removeAttr("disabled");
                $("#btnSave").attr('value', lang_save);
                return;
            }
            
            if($("#btnSave").attr('value') == lang_save) {
                $('#frmCandidateVacancyStatus').submit();
            }
        });
    
});
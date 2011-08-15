$(document).ready(function() {
    $('#cancelBtn').click(function(){
        window.location.replace(cancelBtnUrl+'?id='+candidateId);
    });
    $('#actionBtn').click(function(){
        $('#frmCandidateVacancyStatus').submit();
    });
});
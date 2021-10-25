
$(document).ready(function() {
    // Handle hints



    $('#searchBtn').click(function() {
        $("#empsearch_isSubmitted").val('yes');
        $('#search_form input.inputFormatHint').val('');
        $('#search_form').submit();
    });

    $('#resetBtn').click(function(){
        $("#empsearch_isSubmitted").val('yes');
        $("#searchDirectory_emp_name_empName").val('');
        $("#searchDirectory_job_title").val('0');
        $("#searchDirectory_location").val('0');
        $('#search_form').submit();
    });          
        
}); //ready
    
function submitPage(pageNo) {
    document.frmDirectorySearch.pageNo.value = pageNo;
    document.frmDirectorySearch.hdnAction.value = 'paging';
    $('#search_form input.inputFormatHint').val('');
    $("#empsearch_isSubmitted").val('no');
    document.getElementById('search_form').submit();
}   

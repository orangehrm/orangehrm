$(document).ready(function() {
    
    $("#searchSystemUser_employeeName").autocomplete(employees, {

            formatItem: function(item) {
                           return item.name;
                    }
                    ,matchContains:true
            }).result(function(event, item) {
                $('#searchSystemUser_employeeId').val(item.id);
            }
	);
            
     if ($("#searchSystemUser_employeeName").val() == '') {
        $("#searchSystemUser_employeeName").val(lang_typeforhint)
        .addClass("inputFormatHint");
    }
    
     $('#searchBtn').click(function() {
                  
         $('#search_form').submit();
        
    });
    
    $('#searchSystemUser_employeeName').click(function(){
        if($('#searchSystemUser_employeeName').val() == lang_typeforhint){
            $('#searchSystemUser_employeeName').val("")
            $('#searchSystemUser_employeeName').removeClass("inputFormatHint");
        }
        }); 
    
});

function addSystemUser(){
    window.location.replace(addUserUrl);
}


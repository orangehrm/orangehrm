$(document).ready(function(){
    $("#databaseInfoForm").validate({
        rules: {
            'databaseInfo[host]': "required",
            'databaseInfo[username]': "required",
            'databaseInfo[database_name]': "required"
        },
        messages: {
            'databaseInfo[host]': lang_required,
            'databaseInfo[username]': lang_required,
            'databaseInfo[database_name]': lang_required
        },
        errorPlacement: function(error, element) {
            error.appendTo(element.prev('label'));
            error.appendTo(element.next().next(".errorContainer"));
            error.appendTo(element.next(".errorContainer"));
        }
    });
});
$(document).ready(function() {
   
    $('#btnSave').click(function() {
        if(isValidForm()){
            $('#frmPimCsvImport').submit()
        }
    });
   
   
});

function isValidForm(){
 
    var validator = $("#frmPimCsvImport").validate({

        rules: {
            'pimCsvImport[csvFile]' : {
                required:true
            }

        },
        messages: {
            'pimCsvImport[csvFile]' : {
                required:lang_csvRequired
            }

        },
        errorPlacement: function(error, element) {

            error.appendTo(element.next('div.errorHolder'));

        }

    });
    return true;
}
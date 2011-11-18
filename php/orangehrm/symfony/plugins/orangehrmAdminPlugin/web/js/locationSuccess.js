$(document).ready(function() {
    setCountryState();
    
    //on changing of country
    $("#location_country").change(function() {
        setCountryState();
    });
    
    $('#btnSave').click(function() {
        $('#location_locationId').val(locationId);
        if(isValidForm()){          
            $('#frmLocation').submit();
        }
    });
    
    $('#btnCancel').click(function() {
        window.location.replace(viewLocationUrl);
    });
    
    
    if(locationId > 0){
        $('#locationHeading').text(lang_editLocation);
    }
    
});


function setCountryState() {
    var hide = "display:none;";
    var show = "display:block;";

    $("#location_state").hide();
    $("#location_province").show();

    if($("#location_country").val() == 'US') {
        $("#location_state").show();
        $("#location_province").hide();
    }
}
    
function isValidForm(){
    
    var validator = $("#frmLocation").validate({

        rules: {
            'location[name]' : {
                required:true,
                maxlength: 100
            },
            'location[country]' : {
                required:true,
                maxlength: 3
            },
            'location[province]' : {
                maxlength: 50
            },
            'location[city]' : {
                maxlength: 50
            },
            'location[address]' : {
                maxlength: 255
            },
            'location[zipCode]' : {
                maxlength: 30
            },
            'location[phone]' : {
                maxlength: 30,
                phone: true
            },
            'location[fax]' : {
                maxlength: 30,
                phone: true
            },
            'location[notes]' : {
                maxlength: 255
            }

        },
        messages: {
            'location[name]' : {
                required: lang_LocNameRequired,
                maxlength: lang_Max100Chars
            },
            'location[country]' : {
                required: lang_CountryRequired,
                maxlength: lang_ValidCountry
            },
            'location[province]' : {
                maxlength: lang_Max50Chars
            },
            'location[city]' : {
                maxlength: lang_Max50Chars
            },
            'location[address]' : {
                maxlength: lang_Max255Chars
            },
            'location[zipCode]' : {
                maxlength: lang_Max30Chars
            },
            'location[phone]' : {
                maxlength: lang_Max30Chars,
                phone: lang_validPhoneNo
            },
            'location[fax]' : {
                maxlength: lang_Max30Chars,
                phone: lang_validFaxNo
            },
            'location[notes]' : {
                maxlength: lang_Max255Chars
            }

        },

        errorPlacement: function(error, element) {
            error.appendTo(element.next('div.errorHolder'));
        }

    });
    return true;
}
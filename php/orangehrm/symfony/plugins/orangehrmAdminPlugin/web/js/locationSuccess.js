$(document).ready(function() {
    
    var state_li = "location_state_li";
    var province_li = "location_province_li";
    $('#location_state').parent('li').prop('id', state_li);    
    $('#location_province').parent('li').prop('id', province_li); 
    
    setCountryState();
    
    //on changing of country
    $("#location_country").change(function() {
        setCountryState();
    });
    
    $('#btnSave').click(function() {
        
        if ($('#btnSave').val() == lang_edit){
            enableWidgets();
        } else if ($('#btnSave').val() == lang_save){
            $('#location_locationId').val(locationId);
            if(isValidForm()){          
                $('#frmLocation').submit();
            }
        }
    });
    
    $('#btnCancel').click(function() {
        window.location.replace(viewLocationUrl+'?locationId='+locationId);
    });
    
    
    if(locationId > 0){
        $('#locationHeading').text(lang_editLocation);
        disableWidgets();
    }
    
});

function disableWidgets(){
    $('.formInput').attr('disabled','disabled');
    $('#btnSave').val(lang_edit);  
}

function enableWidgets(){ 
    $('.formInput').removeAttr('disabled');
    $('#btnSave').val(lang_save);
}

function setCountryState() {
    var hide = "display:none;";
    var show = "display:block;";

    if($("#location_country").val() == 'US') {
        $('#location_state_li').show();
        $('#location_province_li').hide();
        
    } else {
        $('#location_state_li').hide();
        $('#location_province_li').show();
        
    }
}
    
function isValidForm(){
    
    $.validator.addMethod("uniqueName", function(value, element, params) {
        var temp = true;
        var currentLocation;
        var id = parseInt(locationId,10);
        var vcCount = locationList.length;
        for (var j=0; j < vcCount; j++) {
            if(id == locationList[j].id){
                currentLocation = j;
            }
        }
        var i;
        vcName = $.trim($('#location_name').val()).toLowerCase();
        for (i=0; i < vcCount; i++) {

            arrayName = locationList[i].name.toLowerCase();
            if (vcName == arrayName) {
                temp = false
                break;
            }
        }
        if(currentLocation != null){
            if(vcName == locationList[currentLocation].name.toLowerCase()){
                temp = true;
            }
        }
		
        return temp;
    });
    
    var validator = $("#frmLocation").validate({

        rules: {
            'location[name]' : {
                required:true,
                uniqueName: true,
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
                maxlength: 250
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
                maxlength: 250
            }

        },
        messages: {
            'location[name]' : {
                required: lang_LocNameRequired,
                uniqueName: lang_uniqueName,
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

        }

    });
    return true;
}
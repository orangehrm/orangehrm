$(document).ready(function() {
	$("#uploaded").hide();
    if(candidateId > 0) {
        $(".formInputText").attr('disabled', 'disabled');
        $(".formInput").attr('disabled', 'disabled');
        $(".contactNo").attr('disabled', 'disabled');
        $(".keyWords").attr('disabled', 'disabled');
        $("#cvHelp").hide();
        $("#uploaded").show();
        $("#btnSave").hide();
    }	
	stretchy(document.getElementById('txtArea'));
	//stretchy($('#txtArea').val());
	var isCollapse = false;
	$("#txtArea").attr('disabled', 'disabled');
	$("#txtArea").hide();

	$('#extend').click(function(){
		if(!isCollapse){
			$("#txtArea").show();
			isCollapse = true;
			$('#extend').text('[-]');
		} else {
			$("#txtArea").hide();
			isCollapse = false;
			$('#extend').text('[+]');
		}
	});
        
	$('#btnSave').click(function() {
           
		if(isValidForm()){ 
			$('#addCandidate_vacancyList').val(vacancyId);
			$('#addCandidate_keyWords.inputFormatHint').val('');
			$('form#frmAddCandidate').attr({
				action:linkForApplyVacancy+"?id="+vacancyId
			});
			$('form#frmAddCandidate').submit();
		}
	});
        

    $('#backLink').click(function(){
        window.location.replace(linkForViewJobs);
    });
	if ($("#addCandidate_keyWords").val() == '') {
		$("#addCandidate_keyWords").val(lang_commaSeparated).addClass("inputFormatHint");
	}

	$("#addCandidate_keyWords").one('focus', function() {

		if ($(this).hasClass("inputFormatHint")) {
			$(this).val("");
			$(this).removeClass("inputFormatHint");
		}
	});

	
});

function stretchy(element) {
	var value= element.value;
	function update() {
		var h= element.scrollHeight;
		if (h>element.offsetHeight || h<element.offsetHeight-48)
			element.style.height= (h+24)+'px';
	}
	element.onkeyup= update;
	setInterval(update, 1000);
	update();
}

function isValidForm(){

	var validator = $("#frmAddCandidate").validate({

		rules: {
			'addCandidate[firstName]' : {
				required:true,
				maxlength:30
			},

			'addCandidate[middleName]' : {
				maxlength:30
			},

			'addCandidate[lastName]' : {
				required:true,
				maxlength:30
			},
			'addCandidate[email]' : {
				required:true,
				email:true,
				maxlength:30

			},

			'addCandidate[contactNo]': {
				phone: true,
				maxlength:30
			},

			'addCandidate[resume]' : {
				required:true
			},

			'addCandidate[keyWords]': {
				maxlength:250
			}
		},
		messages: {
			'addCandidate[firstName]' : {
				required: lang_firstNameRequired,
				maxlength: lang_tooLargeInput
			},

			'addCandidate[middleName]' : {
				maxlength: lang_tooLargeInput
			},


			'addCandidate[lastName]' : {
				required: lang_lastNameRequired,
				maxlength: lang_tooLargeInput
			},

			
            'addCandidate[email]' : {
				required: lang_emailRequired,
				email: lang_validEmail,
				maxlength: lang_tooLargeInput
			},
            
            'addCandidate[contactNo]': {
				phone: lang_validPhoneNo,
				maxlength:lang_tooLargeInput
			},

			'addCandidate[resume]' : {
				required:lang_resumeRequired
			},

			'addCandidate[keyWords]': {
				maxlength:lang_noMoreThan250
			}
		},

		errorPlacement: function(error, element) {

			error.appendTo(element.next('div.errorHolder'));

		}

	});
	return true;
}
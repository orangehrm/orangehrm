var counter;
$(document).ready(function() {
		counter = 1;
	//Load default Mask if empty
	var fromDate = trim($("#jobInterview_date").val());
	if (fromDate == '') {
		$("#jobInterview_date").val(dateDisplayFormat);
	}

	//Bind date picker
	daymarker.bindElement("#jobInterview_date",
	{
		onSelect: function(date){
		//$("#candidateSearch_fromDate").valid();
		},
		dateFormat:jsDateFormat
	});

	$('#frmDateBtn').click(function(){
		daymarker.show("#jobInterview_date");
	});

	//Auto complete
	$(".formInputInterviewer").autocomplete(employees, {
		formatItem: function(item) {
			return item.name;
		},
		matchContains:true
	}).result(function(event, item) {
		//$("#candidateSearch_selectedCandidate").val(item.id);
		//$("label.error").hide();
		validateInterviewerNames()
	});

	$("#addButton").live('click', function(){		
		counter++;
		if(counter == numberOfInterviewers){
			$("#addButton").hide();
		}
		$('#interviewer_'+counter).show();
		alert(counter);
	});
    
	$('.removeText').live('click', function(){
		var result = /\d+(?:\.\d+)?/.exec(this.id);
		$('#interviewer_'+result).hide();
		counter--;
		if(counter < numberOfInterviewers){
			$("#addButton").show();
		}
		alert(counter);
	});

	$('#removeButton1').hide();
	for(var i = 2; i <= numberOfInterviewers; i++){
		$('#interviewer_'+i).hide();
	}
	$('#saveBtn').click(function(){
		//        if(isValidForm()){
		validateInterviewers()
		$('#frmJobInterview').submit();
	//        }
	});

	if(interviewId>0){
		var noOfInterviewers = $('#jobInterview_selectedInterviewerList').val();
		var i;
		for(i=1; i<=noOfInterviewers; i++){
			$('#interviewer_'+(i)).show();
		}
		counter = noOfInterviewers;
	}


});

function validateInterviewers(){

	var empCount = employeeList.length;
	var empIdList = new Array();
	var j = 0;
	$('.formInputInterviewer').each(function(){
		element = $(this);
		inputName = $.trim(element.val()).toLowerCase();
		if(inputName != ""){
			var i;
			for (i=0; i < empCount; i++) {
				arrayName = employeeList[i].name.toLowerCase();

				if (inputName == arrayName) {
					empIdList[j] = employeeList[i].id;
					j++;
					break;
				}
			}
		}
	});
	$('#jobInterview_selectedInterviewerList').val(empIdList);
}

function validateInterviewerNames(){

	var flag = true;
	$(".messageBalloon_success").remove();
	//$(".messageBalloon_failure").remove()
	$('#interviewerNameError').removeAttr('class');
	$('#interviewerNameError').html("");

	var errorStyle = "background-color:#FFDFDF;";
	var normalStyle = "background-color:#FFFFFF;";
	var interviewerNameArray = new Array();
	var errorElements = new Array();
	var index = 0;
	var num = 0;

	$('.formInputInterviewer').each(function(){
		element = $(this);
		$(element).attr('style', normalStyle);
		if(element.val() != ""){
			interviewerNameArray[index] = $(element);
			index++;
		}
	});

	for(var i=0; i<interviewerNameArray.length; i++){
		var currentElement = interviewerNameArray[i];
		for(var j=1+i; j<interviewerNameArray.length; j++){

			if(currentElement.val() == interviewerNameArray[j].val() ){
				errorElements[num] = currentElement;
				errorElements[++num] = interviewerNameArray[j];
				num++;
				$('#interviewerNameError').html(lang_identical_rows);
				flag = false;

			}
		}
		for(var k=0; k<errorElements.length; k++){

			errorElements[k].attr('style', errorStyle);
		}
	}

	return flag;
}
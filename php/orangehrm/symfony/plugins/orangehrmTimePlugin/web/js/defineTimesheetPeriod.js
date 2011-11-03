/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function(){

	$("#btnSave").click(function(){

		if($('#time_startingDays').val()==""){
			$('#btnSave').attr('disabled', 'disabled');
			$('#messagebar').html(required_msge);
			$('#messagebar').attr('class', "messageBalloon_failure");
		}else{
			$('form#definePeriod').attr({
				action:linkTodefineTimesheetPeriod
			});
			$('form#definePeriod').submit();
		}
	});

	$('#time_startingDays').change(function() {
		if($('#time_startingDays').val()==""){
			$('#btnSave').attr('disabled', 'disabled');
			$('#messagebar').html(required_msge);
			$('#messagebar').attr('class', "messageBalloon_failure");
		}else{
			$('#btnSave').removeAttr('disabled');
			$('#messagebar').html("");
			$(".messageBalloon_failure").remove();
		}
	});
});
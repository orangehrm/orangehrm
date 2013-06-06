$(document).ready(function() {
	$('#cancelBtn').click(function(){
		if($("#cancelBtn").attr('value') == lang_back) {
			window.location.replace(cancelBtnUrl+'?id='+candidateId);
		}
		if($("#cancelBtn").attr('value') == lang_cancel) {
			window.location.replace(cancelUrl+'?id='+historyId);
		}
	});
	$('#actionBtn').click(function(){
		$('#frmCandidateVacancyStatus').attr({
			action:linkForchangeCandidateVacancyStatus+"?candidateVacancyId="+candidateVacancyId+'&selectedAction='+selectedAction
		});
		$('#frmCandidateVacancyStatus').submit();
	});

	if(selectedAction == passAction || selectedAction == failAction){
		$("#actionBtn").removeClass('savebutton').addClass('newSaveBtn');
	}

	$('#btnSave').click(function() {
		if($("#btnSave").attr('value') == lang_edit) {
			$(".formInputText").removeAttr("disabled");
			$("#btnSave").attr('value', lang_save);
			$("#cancelBtn").attr('value', lang_cancel);
			return;
		}
            
		if($("#btnSave").attr('value') == lang_save) {
			$('#frmCandidateVacancyStatus').attr({
				action:linkForchangeCandidateVacancyStatus+"?id="+historyId
			});
			$('#frmCandidateVacancyStatus').submit();
		}
	});
    
});
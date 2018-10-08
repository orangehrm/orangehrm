$(document).ready(function () {
    $(".search_employee").click(function () {
        var candidateId = $("#candidate_empId").val()
        if (candidateId > 0) {
            var data = getCandidateData(candidateId)
        }
    });
    $("#modal_confirm").click(function () {
        $("#frmPurgeCandidateRecords").submit();
    });
});

function getCandidateData(id) {
    $.ajax({
        method: "POST",
        data: {vacancyID: id},
        url: ajaxUrl, success: function (result) {
            $("#selected_employee").html(result);
        }
    });
}

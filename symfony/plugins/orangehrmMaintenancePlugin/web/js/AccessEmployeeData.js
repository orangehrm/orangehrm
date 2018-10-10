$(document).ready(function () {
    $(".search_employee").click(function () {
        var emp_id = $("#employee_empId").val()
        if (emp_id > 0) {
            var data = getEmployeeData(emp_id)
        }
    });
    $("#modal_confirm").click(function () {
        $("#frmAccessEmployeeData").submit();
    });

});

function getEmployeeData(id) {
    $.ajax({
        method: "POST",
        data: {empployeeID: id},
        url: ajaxUrl, success: function (result) {
            $("#selected_employee").html(result);
            $("#btnDelete").attr("value", accessData)
        }
    });
}

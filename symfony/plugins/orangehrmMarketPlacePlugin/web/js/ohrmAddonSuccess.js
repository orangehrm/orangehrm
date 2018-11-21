$(document).ready(function () {
    var acc = document.getElementsByClassName("accordion");
    var i;
    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function () {
            this.classList.toggle("active");
            var panel = this.nextElementSibling;
            if (panel.style.display === "block") {
                panel.style.display = "none";
            } else {
                panel.style.display = "block";
            }
        });
    }
    $('.accordion').click(function () {
        var id = $(this).attr('addOnId');
        getDescription(id);
    })
});

function getDescription(id) {
    $.ajax({
        method: "POST",
        data: {addonID: id},
        url: ajaxUrl, success: function (result) {
            $(".panel").html(result);
        }
    });
}

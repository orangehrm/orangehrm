
$(document).ready(function () {
    $("#flipContact").click(function () {
        if ($("#panelContact").filter(":visible").length > 0) {
            $("#panelContact").slideUp("slow");
        } else {
            $("#panelContact").slideDown("slow");
        }

    });
    $("#flipPersonal").click(function () {

        $("#panelPersonal").toggle(300);
        ;
        $("#moreDetails").toggle(300);
        $("#lessDetails").toggle(300);
        $("#panelStat").toggle(300);
        ;
        $("#moreStat").toggle(300);
        $("#lessStat").toggle(300);


    });
    $("#flipJob").click(function () {
        if ($("#panelJob").filter(":visible").length > 0) {
            $("#panelJob").slideUp("slow");
        } else {
            $("#panelJob").slideDown("slow");
        }

    });
    $("#flipStat").click(function () {
        $("#panelPersonal").toggle(300);
        ;
        $("#moreDetails").toggle(300);
        $("#lessDetails").toggle(300);
        $("#panelStat").toggle(300);
        ;
        $("#moreStat").toggle(300);
        $("#lessStat").toggle(300);

    });
});

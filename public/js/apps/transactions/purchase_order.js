$(function () {
    $("#disc").attr("disabled", true);
})

$("#en_disc").click(function () {
    if ($("#en_disc").is(':checked')) {
        $("#disc").attr("disabled", false);
    } else {
        $("#disc").attr("disabled", true);
    }
})

$(".hidePhotoPopUp").click(function (e) {
    var id = e.target.id;
    $("#showPhotos" + id.split("_")[1]).modal('hide');
});

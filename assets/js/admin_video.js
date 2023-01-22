document.getElementById("Video_url_file").onchange = function (event) {
    let videoFile = event.target.files[0];
    let video = document.getElementById("video");
    video.src = URL.createObjectURL(videoFile);
}

document.getElementById("Video_teaser_file").onchange = function (event) {
    let teaserFile = event.target.files[0];
    let teaser = document.getElementById("teaser");
    teaser.src = URL.createObjectURL(teaserFile);
}

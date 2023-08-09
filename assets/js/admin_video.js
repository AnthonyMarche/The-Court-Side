function videoPreview(inputId, mediaPlayerId) {
    let input = document.getElementById(inputId);
    let video = document.getElementById(mediaPlayerId);

    if (input) {
        input.onchange = function (event) {
            let file = event.target.files[0];
            video.classList.remove('d-none');
            video.src = URL.createObjectURL(file);
        }
    }
}

videoPreview("Video_url_file", "video");
videoPreview("Video_teaser_file", "teaser");
videoPreview("teaser_video", "teaser");


if (document.getElementById("teaser_video")) {
    let submitTeaser = document.getElementById('submit-new-teaser');
    let loaderTeaser = document.getElementById('adminLoader');
    let input = document.getElementById("teaser_video")
    let adminBody = document.getElementById("adminBody")

    input.addEventListener('change', function (){
        submitTeaser.removeAttribute('disabled');
    })

    submitTeaser.addEventListener("click", async function () {
        loaderTeaser.classList.remove('d-none');
        adminBody.classList.add('body-admin');
    });
}

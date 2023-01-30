if (document.getElementById("Video_url_file")) {
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

}

if (document.getElementById("teaser_video")) {
    document.getElementById("teaser_video").onchange = function (event) {
        let teaserFile = event.target.files[0];
        let teaser = document.getElementById("teaser");
        teaser.src = URL.createObjectURL(teaserFile);
    }

    let submitTeaser = document.getElementById('submit-new-teaser');
    let loaderTeaser = document.getElementById('adminLoader');
    let input = document.getElementById("teaser_video")
    let adminBody = document.getElementById("adminBody")

    input.addEventListener('change', function (){
        submitTeaser.removeAttribute('disabled');
    })

    submitTeaser.addEventListener("click", async function (e) {
        loaderTeaser.classList.remove('d-none');
        adminBody.classList.add('body-admin');
    });



}



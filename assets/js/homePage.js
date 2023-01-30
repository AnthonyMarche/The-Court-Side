// hover to play on videos
const teaser = document.querySelectorAll(".hover-to-play-teaser");
const video = document.querySelectorAll(".hover-to-play-video");
const delay = ms => new Promise(res => setTimeout(res, ms));

for (let i = 0; i < teaser.length; i++) {
    teaser[i].addEventListener("mouseenter", async function (e) {
        teaser[i].play();
    });
    teaser[i].addEventListener("mouseout", async function (e) {
        teaser[i].currentTime = 0;
        await delay(100);
        teaser[i].pause();
    });
}

for (let i = 0; i < video.length; i++) {
    let play;
    video[i].onmouseover = async function () {
        play = true;
        while (play === true) {
            video[i].play();
            await delay(15000);
            video[i].pause();
            video[i].currentTime = 0;
        }
    }

    video[i].onmouseout = async function () {
        play = false;
        video[i].currentTime = 0;
        await delay(100);
        video[i].pause();
    }
}

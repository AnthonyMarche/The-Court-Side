// hover to play on videos
const clip = document.querySelectorAll(".hover-to-play");
for (let i = 0; i < clip.length; i++) {
    clip[i].addEventListener("mouseenter", function (e) {
        clip[i].play();
    });
    clip[i].addEventListener("mouseout", function (e) {
        clip[i].pause();
    });
}

if (document.getElementById('top-button-container')) {
    const topButton = document.getElementById('top-button-container');
    let firstScroll;
    window.onscroll = () => {
        if (window.scrollY > 300) {
            topButton.classList.remove('top-button-container');
            topButton.classList.add('top-button-container-active');
            firstScroll = false;
        } else if (window.scrollY < 300) {
            topButton.classList.remove('top-button-container-active');
            if (firstScroll === false) {
                topButton.classList.remove('top-button-container');
                topButton.classList.add('top-button-container-inactive');
            }
        }
    }

    topButton.addEventListener('click', function (e) {
        window.scrollTo(0, 0);
    })
}

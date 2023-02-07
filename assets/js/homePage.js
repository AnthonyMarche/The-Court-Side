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
}

const alertPlaceholder = document.getElementById('liveAlertPlaceholder')

const alert = (message, type) => {
    const wrapper = document.createElement('div')
    wrapper.innerHTML = [
        `<div class="alert alert-${type} alert-dismissible pr-0 py-3 d-flex flex-column justify-content-between align-items-center" role="alert">`,
        `   <div class="mr-5 mb-3 text-center"> ${message}</div>`,
        `   <div class="d-flex">`,
        '       <button type="button" class="btn btn-custom mr-2" data-bs-dismiss="alert" aria-label="Close">Accepter</button>',
        '       <button type="button" class="btn btn-danger" data-bs-dismiss="alert" aria-label="Close">Refuser</button>',
        '       <button type="button" class="btn btn-close " data-bs-dismiss="alert" aria-label="Close">X</button>',
        '   </div>',
        '</div>'
    ].join('')

    alertPlaceholder.append(wrapper)
}

if (alertPlaceholder && !sessionStorage.getItem("firstVisit")) {
    var delayInMilliseconds = 3000;
    setTimeout(function () {
        alert('Cookies : Vous pouvez cliquer sur "Accepter" pour accepter les cookies, sur le boutton "Refuser" pour ne pas que vos cookies soit utilisés ou pour continuer sans accepter des cookies non opérationnels, veuillez cliquez sur le bouton X.', 'info')
        sessionStorage.setItem("firstVisit", "false")
    }, delayInMilliseconds);
}

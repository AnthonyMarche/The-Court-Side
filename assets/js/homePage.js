// hover to play on videos
initializePreviewVideo()
function initializePreviewVideo () {
    const videos = document.querySelectorAll(".hover-to-play");
    const delay = ms => new Promise(res => setTimeout(res, ms));
    if (videos) {
        for (let i = 0; i < videos.length; i++) {
            let play;
            videos[i].onmouseover = async function () {
                play = true;
                while (play === true) {
                    videos[i].play();
                    await delay(15000);
                    videos[i].pause();
                    videos[i].currentTime = 0;
                }

            }
            videos[i].onmouseout = async function () {
                play = false;
                videos[i].currentTime = 0;
                await delay(100);
                videos[i].pause();
            }
        }

    }
}
export { initializePreviewVideo };

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

const alertPlaceholder = document.getElementById('liveAlertPlaceholder')

const alert = (message, type) => {
    const wrapper = document.createElement('div')
    wrapper.innerHTML = [
        `<div class="alert alert-${type} alert-dismissible pr-0 pb-3 d-flex flex-column justify-content-between align-items-center" role="alert">`,
        '       <button type="button" class="btn btn-close d-flex align-self-end p-0 pr-2" data-bs-dismiss="alert" aria-label="Close">X</button>',
        `   <div class="mr-5 mb-3 text-center"> ${message}</div>`,
        `   <div class="d-flex">`,
        '       <button type="button" class="btn btn-custom mr-2" data-bs-dismiss="alert" aria-label="Close">Accepter</button>',
        '       <button type="button" class="btn btn-danger" data-bs-dismiss="alert" aria-label="Close">Refuser</button>',
        '   </div>',
        '</div>'
    ].join('')

    alertPlaceholder.append(wrapper)
}

if (alertPlaceholder && !sessionStorage.getItem("firstVisit")) {
    var delayInMilliseconds = 5000;
    setTimeout(function () {
        alert('Cookies : Vous pouvez cliquer sur "Accepter" pour accepter les cookies, sur le boutton "Refuser" pour que vos cookies ne soient pas utilisés ou pour continuer sans accepter les cookies non opérationnels, veuillez cliquez sur le bouton X.', 'info')
        sessionStorage.setItem("firstVisit", "false")
    }, delayInMilliseconds);
}

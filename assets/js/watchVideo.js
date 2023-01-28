if (document.getElementsByClassName('.js-like')) {
    Array.from(document.querySelectorAll('a.js-like')).forEach(function (link) {
        link.addEventListener('click', onClickLink);
    });
    const axios = window.axios;
    // eslint-disable-next-line no-inner-declarations
    async function onClickLink(event) {
        event.preventDefault();
        const url = this.href;
        const icone = this.querySelector('i');
        try {
            const result = await axios.post(url);
            const data = result.data;
        } catch (error) {
            if (error.response.status === 403) {
                window.location = '/login'
            }
        }
    }

    let icon = document.querySelector('.like-icon');
    icon.onclick = function(){
        icon.classList.toggle('active');
    }
}

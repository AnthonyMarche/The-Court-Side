if (document.getElementsByClassName('.js-like')) {
    const LIKE_ICON = 'watch-like-icon bi bi-heart-fill';
    const UNLIKE_ICON = 'watch-like-icon bi bi-heart';
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
            icone.className = icone.className === LIKE_ICON ? UNLIKE_ICON : LIKE_ICON;
        } catch (error) {
            if (error.response.status === 403) {
                window.location = '/login'
            }
        }
    }
}

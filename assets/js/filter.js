if (document.querySelector('.js-content')) {
    const dateFilter = document.getElementById('dateFilter')
    const likesFilter = document.getElementById('likesFilter')
    const viewsFilter = document.getElementById('viewsFilter')
    const content = document.querySelector('.js-content')
    const loadingIcon = document.querySelector('.loading-container')

    dateFilter.addEventListener('click', applyFilter);
    likesFilter.addEventListener('click', applyFilter);
    viewsFilter.addEventListener('click', applyFilter);

    // eslint-disable-next-line no-inner-declarations
    async function applyFilter(event) {
        event.preventDefault()
        let filterLink = event.currentTarget;
        let link = filterLink.href;

        const response = await fetch(link, {
            headers: {
                'X-Requested-with': 'XMLHttpRequest'
            }
        })
        if (response.status >= 200 && response.status < 300) {
            const data = await response.json()
            content.innerHTML = data.content

            //fake loading
            const delay = ms => new Promise(res => setTimeout(res, ms));
            loadingIcon.classList.remove('disappearance');
            loadingIcon.classList.add('apparition');
            await delay(1500);
            loadingIcon.classList.remove('apparition');
            loadingIcon.classList.add('disappearance');

            history.replaceState({}, '', link)
        }
    }
}


if (document.querySelector('.js-content')) {
    const dateFilter = document.getElementById('dateFilter')
    const likesFilter = document.getElementById('likesFilter')
    const viewsFilter = document.getElementById('viewsFilter')
    const content = document.querySelector('.js-content')
    const loadingIcon = document.querySelector('.loading-container')
    const sortingLikes = document.querySelector('.sorted-by-likes')
    const sortingViews = document.querySelector('.sorted-by-views')
    const sortingRecent = document.querySelector('.sorted-by-recent')
    const delay = ms => new Promise(res => setTimeout(res, ms));

    dateFilter.addEventListener('click', applyFilter);
    likesFilter.addEventListener('click', applyFilter);
    viewsFilter.addEventListener('click', applyFilter);

    viewsFilter.addEventListener('click', async function () {
        await delay(1500);
        sortingViews.classList.remove('disappearance');
        sortingRecent.classList.add('disappearance');
        sortingLikes.classList.add('disappearance');
    });
    likesFilter.addEventListener('click', async function () {
        await delay(1500);
        sortingViews.classList.add('disappearance');
        sortingRecent.classList.add('disappearance');
        sortingLikes.classList.remove('disappearance');
    });
    dateFilter.addEventListener('click', async function () {
        await delay(1500);
        sortingViews.classList.add('disappearance');
        sortingRecent.classList.remove('disappearance');
        sortingLikes.classList.add('disappearance');
    });

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
            loadingIcon.classList.remove('disappearance');
            loadingIcon.classList.add('apparition');
            await delay(1500);
            loadingIcon.classList.remove('apparition');
            loadingIcon.classList.add('disappearance');

            history.replaceState({}, '', link)
        }
    }
}


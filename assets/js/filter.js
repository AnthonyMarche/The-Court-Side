import { initializePreviewVideo } from './homePage';

if (document.querySelector('.update-videos')) {
    const baseUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
    const dateFilter = document.getElementById('date-filter');
    const likesFilter = document.getElementById('likes-filter');
    const viewsFilter = document.getElementById('views-filter');
    const currentFilter = document.querySelectorAll('.current-filter');
    const content = document.querySelector('.update-videos');
    const loader = document.querySelector('.loading-container');
    const filters = [dateFilter, likesFilter, viewsFilter];

    // Function to get current selected filter
    function getCurrentFilter() {
        const url = new URL(location.href);
        const urlFilter = url.searchParams.get('sortedBy');
        return filters.find(filter =>
            filter.getAttribute('data-filter') === urlFilter
        );
    }

    // Function to show the loader
    function showLoader() {
        loader.classList.remove('disappearance');
        setTimeout(() => {
            loader.classList.add('disappearance');
        }, 1500);
    }

    // Function to apply the selected filter
    async function applyFilter(event) {
        event.preventDefault();
        const selectedFilter = event.currentTarget;

        const filterName = selectedFilter.getAttribute('data-filter');
        const url = baseUrl + '?sortedBy=' + filterName;

        try {
            await updateVideo(url);

            currentFilter.forEach(filterTag => {
                filterTag.innerHTML = selectedFilter.innerHTML;
            })

            history.pushState({ filtersApplied: true }, '', url);
            initializePreviewVideo()
        } catch (error) {
            content.innerHTML =
                '<h4 style="color:white">An error occurred while loading videos</h4>';
        }
    }

    // Function to update videos based on the selected filter
    async function updateVideo(url) {
        const response = await fetch(url, {
            headers: {
                'X-Requested-with': 'XMLHttpRequest'
            }
        });

        if (response.status >= 200 && response.status < 300) {
            const data = await response.json();
            showLoader();
            content.innerHTML = data.content;
        } else {
            throw new Error('Failed to load videos');
        }
    }

    window.addEventListener('popstate', async () => {
        if (history.state.filtersApplied) {
            const filterToSelect = getCurrentFilter();
            const filterName = filterToSelect.getAttribute('data-filter');
            const url = baseUrl + '?sortedBy=' + filterName;

            try {
                await updateVideo(url);
                initializePreviewVideo();
            } catch (error) {
                content.innerHTML =
                    '<h4 style="color:white">An error occurred while loading videos</h4>';
            }
        }
    });

    const urlFilter = getCurrentFilter();
    currentFilter.forEach(filterTag => {
        filterTag.innerHTML = urlFilter.innerHTML;
    })

    filters.forEach(filter => {
        filter.addEventListener('click', applyFilter);
    });
}

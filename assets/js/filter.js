import { initializePreviewVideo } from './homePage';
import axios from 'axios';

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

        await updateVideo(url);

        currentFilter.forEach(filterTag => {
            filterTag.innerHTML = selectedFilter.innerHTML;
        })

        history.pushState({filtersApplied: true}, '', url);
        initializePreviewVideo()
    }

    // Function to update videos based on the selected filter
    async function updateVideo(url) {
        const config = {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
        }
        await axios.get(url, config)
            .then(function (response) {
                const data = response.data;
                showLoader();
                content.innerHTML = data.content;
            })
            .catch(function () {
                content.innerHTML =
                    '<h4 style="color:white">Une erreur s\'est produite lors du chargement des vidéos</h4>';
            })
    }

    window.addEventListener('popstate', async () => {
        if (history.state.filtersApplied) {
            const filterToSelect = getCurrentFilter();
            const filterName = filterToSelect.getAttribute('data-filter');
            const url = baseUrl + '?sortedBy=' + filterName;

            await updateVideo(url);
            initializePreviewVideo();
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

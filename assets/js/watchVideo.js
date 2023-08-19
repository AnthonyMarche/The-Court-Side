import axios from 'axios';

if (document.getElementsByClassName('.js-like')) {
    const likeButton = document.querySelector('.like-heart')
    const errorContainer = document.querySelector('.errors-container');

    likeButton.addEventListener('click', async function (event) {
        event.preventDefault();
        const url = this.href;
        let icon = document.querySelector('.like-icon');
        const config = {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
        }
        await axios.post(url, config)
            .then(function () {
                icon.classList.toggle('active');
            })
            .catch(function (error) {
                if (error.response.data.error) {
                    errorContainer.innerHTML = `<p>${error.response.data.error}</p>`;
                    errorContainer.classList.remove('d-none');
                } else {
                    errorContainer.innerHTML = `<p>Une erreur est survenue.</p>`;
                    errorContainer.classList.remove('d-none');
                }
                setTimeout(() => {
                    errorContainer.classList.add('d-none');
                    errorContainer.innerHTML = '';
                }, 5000);
            })
    })
}


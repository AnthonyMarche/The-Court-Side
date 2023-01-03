//like system on videos
document.getElementById('watchLike').addEventListener('click', addToLike);

function addToLike(event) {
    event.preventDefault();

    let likeLink = event.currentTarget;
    let link = likeLink.href;

    fetch(link)

        .then(res => res.json())

        .then(function(res) {
            let likeIcon = likeLink.firstElementChild;
            if (res.isLiked) {
                likeIcon.classList.remove('bi-heart');
                likeIcon.classList.add('bi-heart-fill');
            } else {
                likeIcon.classList.remove('bi-heart-fill');
                likeIcon.classList.add('bi-heart');
            }
        });
}

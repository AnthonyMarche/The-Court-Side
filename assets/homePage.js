// hover to play on videos
const clip = document.querySelectorAll(".hover-to-play");
for (let i = 0; i < clip.length; i++) { clip[i].addEventListener("mouseenter", function (e) { clip[i].play();
}); clip[i].addEventListener("mouseout", function (e) { clip[i].pause(); }); }

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

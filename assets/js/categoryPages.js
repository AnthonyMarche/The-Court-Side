//import $ alias for jquery
const $ = window.$;

//fixed nav bar js for the category page
if (document.querySelector('.chip-container')){
    const RAF = requestAnimationFrame
    const $nav = document.querySelector('.chip-container')
    const threshold = $nav.getBoundingClientRect()
    const topButton = document.getElementById('top-button-container');
    let firstScroll;
    let updating = false

    const handleScroll = () => {
        console.info('updating')
        if (window.scrollY >= threshold.top || window.pageYOffset >= threshold.top)
            $nav.classList.add('chip-container--fixed')
        else
            $nav.classList.remove('chip-container--fixed')
        updating = false
    }
    console.log(window.scrollY)
    window.onscroll = () => {
        if (updating) return
        else {
            updating = true
            RAF(handleScroll)
        }
        if (window.scrollY > 301) {
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
}
// slideshow navbar
$('.js-slick').slick({
    variableWidth: true,
    infinite: false,
    prevArrow: '<button class="arrow-button-filter"><i class="arrow left"></i></button>',
    nextArrow: '<button class="arrow-button-filter"><i class="arrow right"></i></button>',
});

//import $ alias for jquery
const $ = window.$;

//fixed nav bar js for the category page
if (document.querySelector('.chip-container')){
    const RAF = requestAnimationFrame
    const $nav = document.querySelector('.chip-container')
    const threshold = $nav.getBoundingClientRect()
    let updating = false

    const handleScroll = () => {
        console.info('updating')
        if (window.scrollY >= threshold.top || window.pageYOffset >= threshold.top)
            $nav.classList.add('chip-container--fixed')
        else
            $nav.classList.remove('chip-container--fixed')
        updating = false
    }

    window.onscroll = () => {
        if (updating) return
        else {
            updating = true
            RAF(handleScroll)
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

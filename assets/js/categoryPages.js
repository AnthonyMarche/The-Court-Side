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

$('.js-video-slick').slick({
    infinite: false,
    speed: 1000,
    slidesToShow: 4,
    slidesToScroll: 4,
    prevArrow: '<button class="arrow-button-filter"><i class="arrow-video left-video"></i></button>',
    nextArrow: '<button class="arrow-button-filter"><i class="arrow-video right-video"></i></button>',
    responsive: [
        {
            breakpoint: 1024,
            settings: {
                slidesToShow: 3,
                slidesToScroll: 3,
                infinite: true,
            }
        },
        {
            breakpoint: 600,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 2
            }
        },
        {
            breakpoint: 480,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1
            }
        }
    ]
});

var images = [
    '/uoc-sports/public/images/carousel/1.png',
    '/uoc-sports/public/images/carousel/2.png',
    '/uoc-sports/public/images/carousel/3.png',
    '/uoc-sports/public/images/carousel/4.png'
];

var currentIndex = 1; // Start at 1 because of duplicate at beginning
var isPlaying = true;
var isTransitioning = false;
var autoSlideInterval = null;
var extendedImages = [];

// DOM elements
var track;
var prevBtn;
var nextBtn;
var playPauseBtn;
var dotsContainer;
var carouselWrapper;

function initElements() {
    track = document.getElementById('carouselTrack');
    prevBtn = document.getElementById('prevBtn');
    nextBtn = document.getElementById('nextBtn');
    playPauseBtn = document.getElementById('playPauseBtn');
    dotsContainer = document.getElementById('carouselDots');
    carouselWrapper = document.querySelector('.carousel-wrapper');
}

function setupCarousel() {
    // Create extended array for infinite loop: [last, first, second, third, fourth, first]
    extendedImages = [
        images[images.length - 1]
    ].concat(images).concat([images[0]]);
    
    // Create slides
    track.innerHTML = '';
    for (var i = 0; i < extendedImages.length; i++) {
        var slide = document.createElement('div');
        slide.className = 'carousel-slide';
        slide.innerHTML = '<img src="' + extendedImages[i] + '" alt="Slide ' + i + '" loading="lazy">';
        track.appendChild(slide);
    }
    
    // Create dots
    dotsContainer.innerHTML = '';
    for (var i = 0; i < images.length; i++) {
        var dot = document.createElement('button');
        dot.className = 'dot';
        if (i === 0) dot.classList.add('active');
        dot.onclick = createDotClickHandler(i + 1);
        dotsContainer.appendChild(dot);
    }
    
    // Set initial position
    updateTrackPosition(false);
}

function createDotClickHandler(slideIndex) {
    return function() {
        goToSlide(slideIndex);
    };
}

function bindEvents() {
    prevBtn.addEventListener('click', prevSlide);
    nextBtn.addEventListener('click', nextSlide);
    playPauseBtn.addEventListener('click', togglePlayPause);
    
    // Mouse hover events
    carouselWrapper.addEventListener('mouseenter', pauseAutoSlide);
    carouselWrapper.addEventListener('mouseleave', resumeAutoSlide);
    
    // Handle transition end
    track.addEventListener('transitionend', handleTransitionEnd);
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') prevSlide();
        if (e.key === 'ArrowRight') nextSlide();
        if (e.key === ' ') {
            e.preventDefault();
            togglePlayPause();
        }
    });
}

function updateTrackPosition(withTransition) {
    if (withTransition === undefined) withTransition = true;
    
    if (!withTransition) {
        track.classList.add('no-transition');
    }
    
    track.style.transform = 'translateX(-' + (currentIndex * 100) + '%)';
    
    if (!withTransition) {
        // Force reflow and remove no-transition class
        track.offsetHeight;
        track.classList.remove('no-transition');
    }
    
    updateDots();
}

function updateDots() {
    var dots = dotsContainer.querySelectorAll('.dot');
    for (var i = 0; i < dots.length; i++) {
        if (i === (currentIndex - 1) % images.length) {
            dots[i].classList.add('active');
        } else {
            dots[i].classList.remove('active');
        }
    }
}

function goToSlide(index) {
    if (isTransitioning) return;
    isTransitioning = true;
    currentIndex = index;
    updateTrackPosition();
}

function nextSlide() {
    goToSlide(currentIndex + 1);
}

function prevSlide() {
    goToSlide(currentIndex - 1);
}

function handleTransitionEnd() {
    isTransitioning = false;
    
    // Handle infinite loop
    if (currentIndex === extendedImages.length - 1) {
        // At duplicate first image at end, jump to actual first
        currentIndex = 1;
        updateTrackPosition(false);
    } else if (currentIndex === 0) {
        // At duplicate last image at start, jump to actual last
        currentIndex = images.length;
        updateTrackPosition(false);
    }
}

function startAutoSlide() {
    if (isPlaying) {
        autoSlideInterval = setInterval(function() {
            if (!isTransitioning) {
                nextSlide();
            }
        }, 5000);
    }
}

function stopAutoSlide() {
    if (autoSlideInterval) {
        clearInterval(autoSlideInterval);
        autoSlideInterval = null;
    }
}

function pauseAutoSlide() {
    stopAutoSlide();
}

function resumeAutoSlide() {
    if (isPlaying) {
        startAutoSlide();
    }
}

function togglePlayPause() {
    isPlaying = !isPlaying;
    playPauseBtn.innerHTML = isPlaying ? '<i class="fa-solid fa-pause"></i>' : '<i class="fa-solid fa-play"></i>';
    
    if (isPlaying) {
        startAutoSlide();
    } else {
        stopAutoSlide();
    }
}

function initCarousel() {
    initElements();
    setupCarousel();
    bindEvents();
    startAutoSlide();
}

// Initialize carousel when DOM is loaded
document.addEventListener('DOMContentLoaded', initCarousel);
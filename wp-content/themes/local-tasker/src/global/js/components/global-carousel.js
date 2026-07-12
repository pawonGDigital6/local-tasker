import "swiper/css/bundle";
import Swiper from "swiper/bundle";

// ---------------------------------- Project Gallery
function projectGallery() {
  document.querySelectorAll(".lt-project-gallery").forEach((projGallerSec) => {
    const mainSlide = projGallerSec.querySelector(".gallery-main-slide");
    const slideThumb = projGallerSec.querySelector(".gallery-thumbnail");

    const thumbSlideSwiper = new Swiper(slideThumb, {
      speed: 500,
      spaceBetween: 8,
      slidesPerView: 5,
      slideToClickedSlide: true,
      breakpoints: {
        768: {
          slidesPerView: 6,
          spaceBetween: 24,
        },
      },
    });

    const mainSlideSwiper = new Swiper(mainSlide, {
      speed: 500,
      spaceBetween: 24,
      slidesPerView: 1,
      thumbs: {
        swiper: thumbSlideSwiper,
      },
      navigation: {
        nextEl: ".main-slide-nav .slide-nav.next",
        prevEl: ".main-slide-nav .slide-nav.prev",
      },
    });

    // 3. Sync them together for this specific instance
    //  mainSlideSwiper.controller.control = thumbSlideSwiper;
    //  thumbSlideSwiper.controller.control = mainSlideSwiper;
  });
}

// ---------------------------------- Related Carousel
function relatedCarousel() {
  // Loop through every instance of the component container
  document.querySelectorAll(".related-post").forEach((container) => {
    // 1. Initialize Left Swiper (Scoped to this container)
    const swiper1 = new Swiper(
      container.querySelector(".related-post-carousel"),
      {
        speed: 500,
        spaceBetween: 16,
        slidesPerView: 1.26,
        pagination: {
          // Finds the pagination inside THIS container only
          el: container.querySelector(".swiper-pagination"),
          clickable: true,
        },
        breakpoints: {
          768: {
            slidesPerView: 2,
            spaceBetween: 28,
          },
          992: {
            spaceBetween: 28,
            slidesPerView: 3,
          },
        },
      },
    );
  });
}

export { relatedCarousel, projectGallery };

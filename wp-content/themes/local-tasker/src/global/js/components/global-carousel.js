import Swiper from "swiper/bundle";
import "swiper/css/bundle";

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

export default relatedCarousel;

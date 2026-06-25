import Swiper from "swiper/bundle";
import "swiper/css/bundle";

// Loop through every instance of the component container
document.querySelectorAll(".lt-inner-two-col-hero").forEach((container) => {
  // 1. Initialize Left Swiper (Scoped to this container)
  const swiper1 = new Swiper(container.querySelector(".in-testimonial"), {
    speed: 500,
    spaceBetween: 24,
    slidesPerView: 1,
    pagination: {
      // Finds the pagination inside THIS container only
      el: container.querySelector(".lt-slide-pagination"),
      clickable: true,
    },
  });
});
